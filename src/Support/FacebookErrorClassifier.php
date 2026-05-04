<?php

    declare(strict_types=1);

    namespace Anibalealvarezs\FacebookGraphApi\Support;

    use Exception;
    use GuzzleHttp\Exception\RequestException;

    final class FacebookErrorClassifier
    {
        /**
         * @param mixed $input
         * @return array
         */
        public static function normalize(mixed $input): array
        {
            $payload = self::extractPayload($input);
            $error = is_array($payload['error'] ?? null) ? $payload['error'] : [];

            return [
                'message'      => self::normalizeString($error['message'] ?? null) ?? self::extractMessageFallback($input),
                'type'         => self::normalizeString($error['type'] ?? null),
                'code'         => self::normalizeInt($error['code'] ?? null),
                'subcode'      => self::normalizeInt($error['error_subcode'] ?? null),
                'is_transient' => filter_var($error['is_transient'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'fbtrace_id'   => self::normalizeString($error['fbtrace_id'] ?? null),
                'raw'          => $error,
            ];
        }

        /**
         * @param mixed $input
         * @return array
         */
        public static function classify(mixed $input): array
        {
            $normalized = self::normalize($input);
            $message = strtolower($normalized['message'] ?? '');
            $type = strtolower($normalized['type'] ?? '');
            $code = $normalized['code'];
            $subcode = $normalized['subcode'];

            if (
                $normalized['is_transient']
                || in_array($code, [1, 2, 4, 17, 32, 613], true)
                || str_contains($message, 'please retry your request later')
                || str_contains($message, 'rate limit reached')
                || str_contains($message, 'too many requests')
                || str_contains($message, 'application request limit reached')
            ) {
                return [
                    'category'             => 'retryable',
                    'reason'               => 'facebook_transient',
                    'should_retry'         => true,
                    'should_refresh_token' => false,
                    'delay_ms'             => 1000,
                ];
            }

            if (
                $type === 'oauthexception'
                && (
                    in_array($subcode, [458, 459, 460, 463, 464, 467], true)
                    || str_contains($message, 'session has expired')
                    || str_contains($message, 'access token has expired')
                    || str_contains($message, 'error validating access token')
                    || str_contains($message, 'invalid oauth access token')
                )
            ) {
                return [
                    'category'             => 'auth',
                    'reason'               => 'facebook_auth_expired',
                    'should_retry'         => false,
                    'should_refresh_token' => true,
                    'delay_ms'             => 0,
                ];
            }

            if (
                str_contains($message, 'permissions error')
                || str_contains($message, 'permission')
                || str_contains($message, 'requires')
            ) {
                return [
                    'category'             => 'permission',
                    'reason'               => 'facebook_permission',
                    'should_retry'         => false,
                    'should_refresh_token' => false,
                    'delay_ms'             => 0,
                ];
            }

            return [
                'category'             => 'unknown',
                'reason'               => 'facebook_unknown',
                'should_retry'         => false,
                'should_refresh_token' => false,
                'delay_ms'             => 0,
            ];
        }

        /**
         * @param mixed $input
         * @return bool
         */
        public static function isRetryable(mixed $input): bool
        {
            return self::classify($input)['should_retry'] === true;
        }

        /**
         * @param mixed $input
         * @return array
         */
        private static function extractPayload(mixed $input): array
        {
            if (is_array($input)) {
                return $input;
            }

            if ($input instanceof RequestException && $input->hasResponse()) {
                $body = $input->getResponse()->getBody();
                $body->rewind();
                $contents = json_decode($body->getContents(), true);
                $body->rewind();

                return is_array($contents) ? $contents : [];
            }

            if (is_string($input)) {
                $contents = json_decode($input, true);

                return is_array($contents) ? $contents : [];
            }

            return [];
        }

        /**
         * @param mixed $input
         * @return ?string
         */
        private static function extractMessageFallback(mixed $input): ?string
        {
            if ($input instanceof Exception) {
                return $input->getMessage();
            }

            return self::normalizeString($input);
        }

        /**
         * @param mixed $value
         * @return ?string
         */
        private static function normalizeString(mixed $value): ?string
        {
            if (!is_string($value) && !is_numeric($value)) {
                return null;
            }

            $normalized = trim((string)$value);

            return $normalized === '' ? null : $normalized;
        }

        /**
         * @param mixed $value
         * @return ?int
         */
        private static function normalizeInt(mixed $value): ?int
        {
            if ($value === null || $value === '') {
                return null;
            }

            if (is_int($value)) {
                return $value;
            }

            if (is_numeric($value)) {
                return (int)$value;
            }

            return null;
        }
    }


