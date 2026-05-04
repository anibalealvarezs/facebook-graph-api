<?php

    namespace Anibalealvarezs\FacebookGraphApi\Support;

    final class FacebookInsightMetricGuard
    {
        private const array SPECIALIZED_METRIC_FAMILIES = [
            'clicks'          => ['navigation', 'post_clicks'],
            'video_views'     => ['video_views', 'post_video_views'],
            'reel_watch_time' => ['ig_reels_avg_watch_time', 'ig_reels_video_view_total_time', 'post_video_avg_time_watched'],
        ];

        private const array EXCLUSION_MATRIX = [
            'feed_static'  => ['clicks', 'video_views', 'reel_watch_time'],
            'feed_video'   => ['clicks', 'reel_watch_time'],
            'reel'         => ['clicks', 'video_views'],
            'story_static' => ['video_views', 'reel_watch_time'],
            'story_video'  => ['reel_watch_time'],
            'ad_static'    => ['clicks', 'video_views', 'reel_watch_time'],
            'ad_video'     => ['clicks', 'reel_watch_time'],
            'unknown'      => ['clicks', 'video_views', 'reel_watch_time'],
        ];

        /**
         * @param array<int, string> $metrics
         * @param array<string, mixed>|null $assetData
         * @return array<int, string>
         */
        public static function filterMetrics(array $metrics, ?array $assetData = null): array
        {
            $normalizedMetrics = array_values(array_unique(array_filter(array_map(
                static fn($metric) => trim((string)$metric),
                $metrics
            ))));

            if ($normalizedMetrics === [] || empty($assetData)) {
                return $normalizedMetrics;
            }

            $unsupported = self::resolveUnsupportedMetrics($assetData);
            if ($unsupported === []) {
                return $normalizedMetrics;
            }

            return array_values(array_filter(
                $normalizedMetrics,
                static fn($metric) => !in_array($metric, $unsupported, true)
            ));
        }

        /**
         * @param array<string, mixed>|null $assetData
         * @return array<int, string>
         */
        public static function resolveUnsupportedMetrics(?array $assetData = null): array
        {
            if (empty($assetData)) {
                return [];
            }

            $profile = self::resolveAssetProfile(
                mediaType: (string)($assetData['media_type'] ?? $assetData['type'] ?? ''),
                mediaProductType: (string)($assetData['media_product_type'] ?? '')
            );

            $unsupported = [];
            foreach (self::EXCLUSION_MATRIX[$profile] ?? self::EXCLUSION_MATRIX['unknown'] as $family) {
                $unsupported = array_merge($unsupported, self::SPECIALIZED_METRIC_FAMILIES[$family] ?? []);
            }

            return array_values(array_unique($unsupported));
        }

        public static function resolveAssetProfile(?string $mediaType = null, ?string $mediaProductType = null): string
        {
            $mediaType = strtoupper(trim((string)$mediaType));
            $mediaProductType = strtoupper(trim((string)$mediaProductType));

            if ($mediaProductType === 'REELS' || $mediaType === 'REEL') {
                return 'reel';
            }

            if ($mediaProductType === 'STORY') {
                return $mediaType === 'VIDEO' ? 'story_video' : 'story_static';
            }

            if ($mediaProductType === 'AD') {
                if ($mediaType === 'VIDEO' || $mediaType === 'REEL') {
                    return 'ad_video';
                }

                return in_array($mediaType, ['IMAGE', 'CAROUSEL_ALBUM', ''], true) ? 'ad_static' : 'unknown';
            }

            if ($mediaProductType === 'FEED') {
                if ($mediaType === 'VIDEO') {
                    return 'feed_video';
                }

                if ($mediaType === 'REEL') {
                    return 'reel';
                }

                return in_array($mediaType, ['IMAGE', 'CAROUSEL_ALBUM', ''], true) ? 'feed_static' : 'unknown';
            }

            if ($mediaType === 'VIDEO') {
                return 'feed_video';
            }

            if (in_array($mediaType, ['IMAGE', 'CAROUSEL_ALBUM'], true)) {
                return 'feed_static';
            }

            return 'unknown';
        }
    }

