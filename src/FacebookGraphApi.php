<?php

namespace Anibalealvarezs\FacebookGraphApi;

use Anibalealvarezs\ApiSkeleton\Clients\BearerTokenClient;
use Anibalealvarezs\FacebookGraphApi\Enums\AdAccountPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\AdField;
use Anibalealvarezs\FacebookGraphApi\Enums\AdPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\AdsetField;
use Anibalealvarezs\FacebookGraphApi\Enums\AdsetPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\CampaignField;
use Anibalealvarezs\FacebookGraphApi\Enums\CampaignPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\CreativeField;
use Anibalealvarezs\FacebookGraphApi\Enums\CreativePermission;
use Anibalealvarezs\FacebookGraphApi\Enums\InstagramMediaField;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaProductType;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaType;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricGroup;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\Metric;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;
use Anibalealvarezs\FacebookGraphApi\Enums\PagePermission;
use Anibalealvarezs\FacebookGraphApi\Enums\FacebookPostField;
use Anibalealvarezs\FacebookGraphApi\Enums\FacebookPostPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\TokenSample;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricSet;
use Anibalealvarezs\FacebookGraphApi\Enums\UserPermission;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Anibalealvarezs\FacebookGraphApi\Exceptions\FacebookRateLimitException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\GuzzleException;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class FacebookGraphApi extends BearerTokenClient
{
    protected string $appId;
    protected string $appSecret;
    protected ?string $pageId;
    protected ?string $userAccessToken;
    protected ?string $longLivedUserAccessToken;
    protected ?string $appAccessToken;
    protected ?string $pageAccesstoken;
    protected ?string $longLivedPageAccesstoken;
    protected ?string $clientAccesstoken;
    protected ?string $longLivedClientAccesstoken;
    protected string $tokenPath = "";
    protected string $tokenIdentifier = "";
    protected string $apiVersion = "";
    protected int $sleep = 1000000;
    protected ?FacebookGraphAuth $auth;
    protected array $storedTokens = [];

    /**
     * @param string $userId
     * @param string $appId
     * @param string $appSecret
     * @param string $redirectUrl
     * @param string|null $pageId
     * @param string|null $userAccessToken
     * @param string|null $longLivedUserAccessToken
     * @param string|null $appAccessToken
     * @param string|null $pageAccesstoken
     * @param string|null $longLivedPageAccesstoken
     * @param string|null $clientAccesstoken
     * @param string|null $longLivedClientAccesstoken
     * @param Client|null $guzzleClient
     * @param FacebookGraphAuth|null $auth
     * @param string $tokenPath
     * @param string $tokenIdentifier
     * @param string $apiVersion
     * @param int $sleep
     * @param LoggerInterface|null $logger
     * @throws Exception
     */
    public function __construct(
        string $userId,
        string $appId,
        string $appSecret,
        string $redirectUrl,
        ?string $pageId = null,
        ?string $userAccessToken = null,
        ?string $longLivedUserAccessToken = null,
        ?string $appAccessToken = null,
        ?string $pageAccesstoken = null,
        ?string $longLivedPageAccesstoken = null,
        ?string $clientAccesstoken = null,
        ?string $longLivedClientAccesstoken = null,
        ?Client $guzzleClient = null,
        ?FacebookGraphAuth $auth = null,
        string $tokenPath = "",
        string $tokenIdentifier = "",
        string $apiVersion = 'v25.0',
        int $sleep = 200000,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger;
        parent::__construct(
            baseUrl: 'https://graph.facebook.com/',
            token: 'placeholder',
            authSettings: [
                'location' => 'query',
                'name' => 'access_token',
            ],
            guzzleClient: $guzzleClient,
            logger: $logger,
        );

        if (!$userId) {
            throw new InvalidArgumentException('User ID is required');
        }
        $this->setUserId($userId);
        $this->apiVersion = $apiVersion;
        $this->sleep = $sleep;
        $this->setAppId($appId);
        $this->pageId = $pageId;
        $this->setAppSecret($appSecret);
        $this->setRedirectUrl($redirectUrl);
        $this->setUserAccessToken($userAccessToken);
        $this->setLongLivedUserAccessToken($longLivedUserAccessToken);
        $this->setAppAccessToken($appAccessToken);
        $this->setPageAccesstoken($pageAccesstoken);
        $this->setLongLivedPageAccesstoken($longLivedPageAccesstoken);
        $this->setClientAccesstoken($clientAccesstoken);
        $this->setLongLivedClientAccesstoken($longLivedClientAccesstoken);
        $this->auth = $auth ?? new FacebookGraphAuth($guzzleClient);

        $this->tokenPath = $tokenPath;
        $this->tokenIdentifier = $tokenIdentifier ?: ($appId ? 'App_' . $appId : "");

        // Load tokens from storage if missing
        if ($this->tokenPath && file_exists($this->tokenPath)) {
            $this->storedTokens = json_decode(json: (string) file_get_contents($this->tokenPath), associative: true) ?: [];
            $serviceKey = $this->getServiceKey();
            if (isset($this->storedTokens[$userId][$serviceKey])) {
                $tokens = $this->storedTokens[$userId][$serviceKey];
                $this->longLivedUserAccessToken = $this->longLivedUserAccessToken ?: ($tokens['long_lived_user'] ?? null);
                $this->appAccessToken = $this->appAccessToken ?: ($tokens['app'] ?? null);
                $this->longLivedClientAccesstoken = $this->longLivedClientAccesstoken ?: ($tokens['long_lived_client'] ?? null);
                if ($this->getPageId() && isset($tokens['long_lived_pages'][$this->getPageId()])) {
                    $this->longLivedPageAccesstoken = $this->longLivedPageAccesstoken ?: $tokens['long_lived_pages'][$this->getPageId()];
                }
            }
        }

        $this->setResponseErrorDetector('error');
        $this->setRateLimitDetector([
            '(#4)',
            '(#17)',
            '(#32)',
            '(#613)',
            'Application request limit reached',
            'Rate limit reached',
            'Too many requests',
            'is_transient":true',
            'is_transient\":true',
            '"code":4',
            '"code":17',
            '"code":32',
            '"code":613'
        ]);
        $this->setErrorMessageParser(fn ($data) => $data['error']['message'] ?? json_encode($data));
    }

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getPageId(): ?string
    {
        return $this->pageId;
    }

    public function setPageId(?string $pageId): void
    {
        $this->pageId = $pageId;
        $this->longLivedPageAccesstoken = null; // Clean to avoid leak

        if ($pageId && !empty($this->storedTokens)) {
            // Deep recursive search to bypass App_... and case-mismatches
            $this->longLivedPageAccesstoken = $this->findTokenDeeply($this->storedTokens, $pageId);
        }
    }

    protected function findTokenDeeply(array $data, string $pageId): ?string
    {
        // Check current level
        if (isset($data['long_lived_pages'][$pageId])) {
            return $data['long_lived_pages'][$pageId];
        }

        // Recurse deeper
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $result = $this->findTokenDeeply($value, $pageId);
                if ($result) {
                    return $result;
                }
            }
        }

        return null;
    }

    public function getBaseUrl(): string
    {
        return parent::getBaseUrl() . ($this->apiVersion ? $this->apiVersion . '/' : '');
    }

    public function getAppSecret(): string
    {
        return $this->appSecret;
    }

    public function setAppSecret(string $appSecret): void
    {
        $this->appSecret = $appSecret;
    }

    public function getUserAccessToken(): ?string
    {
        return $this->userAccessToken;
    }

    public function setUserAccessToken(?string $userAccessToken): void
    {
        $this->userAccessToken = $userAccessToken;
    }

    public function getLongLivedUserAccessToken(): ?string
    {
        return $this->longLivedUserAccessToken;
    }

    public function setLongLivedUserAccessToken(?string $longLivedUserAccessToken): void
    {
        $this->longLivedUserAccessToken = $longLivedUserAccessToken;

        if ($this->tokenPath && $longLivedUserAccessToken) {
            $this->persistToken('long_lived_user', $longLivedUserAccessToken);
        }
    }

    public function getAppAccessToken(): ?string
    {
        return $this->appAccessToken;
    }

    public function setAppAccessToken(?string $appAccessToken): void
    {
        $this->appAccessToken = $appAccessToken;

        if ($this->tokenPath && $appAccessToken) {
            $this->persistToken('app', $appAccessToken);
        }
    }

    public function getPageAccesstoken(): ?string
    {
        return $this->pageAccesstoken;
    }

    public function setPageAccesstoken(?string $pageAccesstoken): void
    {
        $this->pageAccesstoken = $pageAccesstoken;
    }

    public function getLongLivedPageAccesstoken(): ?string
    {
        return $this->longLivedPageAccesstoken;
    }

    public function setLongLivedPageAccesstoken(?string $longLivedPageAccesstoken): void
    {
        $this->longLivedPageAccesstoken = $longLivedPageAccesstoken;

        if ($this->tokenPath && $longLivedPageAccesstoken) {
            $this->persistToken('long_lived_page', $longLivedPageAccesstoken);
        }
    }

    public function getClientAccesstoken(): ?string
    {
        return $this->clientAccesstoken;
    }

    public function setClientAccesstoken(?string $clientAccesstoken): void
    {
        $this->clientAccesstoken = $clientAccesstoken;
    }

    public function getLongLivedClientAccesstoken(): ?string
    {
        return $this->longLivedClientAccesstoken;
    }

    public function setLongLivedClientAccesstoken(?string $longLivedClientAccesstoken): void
    {
        $this->longLivedClientAccesstoken = $longLivedClientAccesstoken;

        if ($this->tokenPath && $longLivedClientAccesstoken) {
            $this->persistToken('long_lived_client', $longLivedClientAccesstoken);
        }
    }

    public function getTokenPath(): string
    {
        return $this->tokenPath;
    }

    public function setTokenPath(string $tokenPath): void
    {
        $this->tokenPath = $tokenPath;
    }

    public function getTokenIdentifier(): string
    {
        return $this->tokenIdentifier;
    }

    public function setTokenIdentifier(string $tokenIdentifier): void
    {
        $this->tokenIdentifier = $tokenIdentifier;
    }

    /**
     * @param string $type
     * @param string|null $token
     * @return void
     */
    protected function persistToken(string $type, ?string $token): void
    {
        if (!$this->tokenPath || !$token) {
            return;
        }

        $data = [];
        if (file_exists($this->tokenPath)) {
            $data = json_decode(json: (string) file_get_contents($this->tokenPath), associative: true) ?: [];
        }

        $userId = $this->getUserId();
        if (!isset($data[$userId]) || !is_array($data[$userId])) {
            $data[$userId] = [];
        }

        $serviceKey = $this->getServiceKey();
        
        // Deep ensure structure for serviceKey to avoid destroying data
        if (!isset($data[$userId])) {
            $data[$userId] = [];
        }
        
        // Check case-insensitive existence of serviceKey
        $existingKey = null;
        foreach (array_keys($data[$userId]) as $k) {
            if (strtolower((string) $k) === strtolower($serviceKey)) {
                $existingKey = $k;
                break;
            }
        }
        
        $keyToUse = $existingKey ?: $serviceKey;
        if (!isset($data[$userId][$keyToUse]) || !is_array($data[$userId][$keyToUse])) {
            $data[$userId][$keyToUse] = [];
        }

        if ($type === 'long_lived_page' && $this->getPageId()) {
            if (!isset($data[$userId][$keyToUse]['long_lived_pages']) || !is_array($data[$userId][$keyToUse]['long_lived_pages'])) {
                $data[$userId][$keyToUse]['long_lived_pages'] = [];
            }
            $data[$userId][$keyToUse]['long_lived_pages'][$this->getPageId()] = $token;
        } else {
            $data[$userId][$keyToUse][$type] = $token;
        }

        // Final consistency check: if the main facebook_marketing node has the token, update it too
        if (isset($data['facebook_marketing']) && $type !== 'long_lived_page') {
            $data['facebook_marketing']['access_token'] = $token;
        }

        // Ensure directory exists
        $dir = dirname($this->tokenPath);
        if (!is_dir($dir)) {
            mkdir(directory: $dir, permissions: 0755, recursive: true);
        }

        file_put_contents(filename: $this->tokenPath, data: json_encode(value: $data, flags: JSON_PRETTY_PRINT));
        $this->storedTokens = $data;
    }

    /**
     * @return string
     */
    protected function getServiceKey(): string
    {
        return $this->tokenIdentifier ?: 'app_' . $this->getAppId();
    }

    /**
     * @param array $relativeUrls
     * @param TokenSample $tokenSample
     * @return array
     * @throws Exception
     */
    public function getBatch(array $relativeUrls, TokenSample $tokenSample = TokenSample::USER): array
    {
        $this->setSampleBasedToken($tokenSample);
        $token = $this->getTokenFromSample($tokenSample);

        $batch = array_map(function ($url) use ($token) {
            $separator = str_contains($url, '?') ? '&' : '?';
            return [
                'method' => 'GET',
                'relative_url' => ltrim((string) $url, '/') . $separator . 'access_token=' . $token,
            ];
        }, $relativeUrls);

        $query = [
            'batch' => json_encode($batch),
        ];

        $response = $this->performRequest(
            method: 'POST',
            endpoint: '',
            form_params: $query,
            tokenSample: $tokenSample,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    public function performRequest(
        string $method,
        string $endpoint,
        array $query = [],
        array|string $body = "",
        array $form_params = [],
        string $baseUrl = "",
        array $headers = [],
        array $additionalHeaders = [],
        ?CookieJar $cookies = null,
        bool $verify = false,
        bool $allowNewToken = true,
        string $pathToSave = "",
        ?bool $stream = null,
        mixed $errorMessageNesting = null,
        int $sleep = 0,
        array $customErrors = [],
        bool $ignoreAuth = false,
        mixed $onFailure = null,
        TokenSample $tokenSample = TokenSample::USER,
    ): mixed {
        $this->setSampleBasedToken($tokenSample);

        if ($this->logger) {
            $this->logger->debug("FB SDK Request: $method " . ($baseUrl ?: $this->getBaseUrl()) . $endpoint . " - Query: " . json_encode($query));
        }

        $result = parent::performRequest(
            method: $method,
            endpoint: $endpoint,
            query: $query,
            body: $body,
            form_params: $form_params,
            baseUrl: $baseUrl,
            headers: $headers,
            additionalHeaders: $additionalHeaders,
            cookies: $cookies,
            verify: $verify,
            allowNewToken: $allowNewToken,
            pathToSave: $pathToSave,
            stream: $stream,
            errorMessageNesting: $errorMessageNesting,
            sleep: $sleep,
            customErrors: $customErrors,
            ignoreAuth: $ignoreAuth,
            onFailure: $onFailure,
        );

        if ($this->logger && $result instanceof \Psr\Http\Message\ResponseInterface) {
            $body = $result->getBody()->getContents();
            $result->getBody()->rewind();
            $this->logger->debug("FB SDK Response: " . substr($body, 0, 1000));
        }

        return $result;
    }

    /**
     * @param Exception $exception
     * @param mixed $onFailure
     * @return mixed
     * @throws Exception
     */
    protected function handleException(Exception $exception, mixed $onFailure = null): mixed
    {
        if ($exception instanceof ApiRequestException) {
            $message = $exception->getMessage();
            if (str_contains($message, '(#4)') || str_contains($message, 'Application request limit reached')) {
                $usageHeaders = [];
                $previous = $exception->getPrevious();
                if ($previous instanceof RequestException && $previous->hasResponse()) {
                    $response = $previous->getResponse();
                    foreach (['X-App-Usage', 'X-Ad-Account-Usage', 'X-Business-Use-Case-Usage'] as $header) {
                        if ($response->hasHeader($header)) {
                            $usageHeaders[$header] = $response->getHeaderLine($header);
                        }
                    }
                }
                throw new FacebookRateLimitException($message, 4, $exception, array_filter($usageHeaders));
            }
        }
        return parent::handleException($exception, $onFailure);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function setSampleBasedToken(TokenSample $tokenSample): void
    {
        $guzzleClient = $this->guzzleClient;

        if ($tokenSample === TokenSample::USER) {
            if (!$this->getLongLivedUserAccessToken() || ($this->getLongLivedUserAccessToken() === 'placeholder')) {
                $userToken = trim($this->getUserAccessToken() ?: '');
                try {
                    $tokenResponse = (new FacebookGraphAuth($guzzleClient))->getLongLivedUserAccessToken(
                        $this->getAppId(),
                        $this->getAppSecret(),
                        $userToken,
                    );
                    $this->setLongLivedUserAccessToken($tokenResponse['access_token']);
                } catch (Exception $e) {
                    $this->setLongLivedUserAccessToken($userToken);
                }
            }
        } elseif ($tokenSample === TokenSample::APP) {
            if (!$this->getAppAccessToken() || ($this->getAppAccessToken() === 'placeholder')) {
                $tokenResponse = (new FacebookGraphAuth($guzzleClient))->getAppAccessToken(
                    $this->getAppId(),
                    $this->getAppSecret(),
                );
                $this->setAppAccessToken($tokenResponse['access_token']);
            }
        } elseif ($tokenSample === TokenSample::PAGE) {
            if (!$this->getLongLivedPageAccesstoken() || ($this->getLongLivedPageAccesstoken() === 'placeholder')) {
                if (!$this->getLongLivedUserAccessToken() || ($this->getLongLivedUserAccessToken() === 'placeholder')) {
                    $userToken = trim($this->getUserAccessToken() ?: '');
                    try {
                        $tokenResponse = (new FacebookGraphAuth($guzzleClient))->getLongLivedUserAccessToken(
                            $this->getAppId(),
                            $this->getAppSecret(),
                            $userToken,
                        );
                        $this->setLongLivedUserAccessToken($tokenResponse['access_token']);
                    } catch (Exception $e) {
                        $this->setLongLivedUserAccessToken($userToken);
                    }
                }
                $userToken = $this->getLongLivedUserAccessToken() ?: $this->getUserAccessToken();
                if (!$userToken || $userToken === 'placeholder') {
                    throw new Exception("Missing or invalid user access token for page token resolution.");
                }

                $targetPageId = trim((string)$this->getPageId());
                $allPages = [];
                $after = null;
                $auth = $this->auth ?: new FacebookGraphAuth($guzzleClient);
                $userId = ($this->getUserId() && $this->getUserId() !== 'system') ? $this->getUserId() : 'me';
                
                do {
                    $endpoint = $userId . '/accounts';
                    $query = [
                        'access_token' => $userToken,
                        'limit' => 100
                    ];
                    if ($after) {
                        $query['after'] = $after;
                    }

                    $response = $auth->performRequest(
                        method: 'GET',
                        endpoint: $endpoint,
                        query: $query
                    );
                    $tokenResponse = json_decode($response->getBody()->getContents(), true);
                    
                    $allPages = array_merge($allPages, $tokenResponse['data'] ?? []);
                    $after = $tokenResponse['paging']['cursors']['after'] ?? null;
                    
                    // Check if target page is in current batch
                    $pageMatch = array_filter(
                        $tokenResponse['data'] ?? [],
                        fn ($p) => trim((string) ($p['id'] ?? '')) === $targetPageId
                    );
                    
                    if (!empty($pageMatch)) {
                        $pageMatch = array_values($pageMatch);
                        $this->setLongLivedPageAccesstoken($pageMatch[0]['access_token']);
                        return; // Found it!
                    }
                } while ($after && !empty($tokenResponse['data']));

                $available = array_map(fn($p) => "{$p['name']} ({$p['id']})", $allPages);
                throw new Exception("Page ID '{$targetPageId}' not found in Meta account. Available pages: " . implode(', ', $available));
            }
        } elseif ($tokenSample === TokenSample::CLIENT) {
            if (!$this->getLongLivedClientAccesstoken() || ($this->getLongLivedClientAccesstoken() === 'placeholder')) {
                $tokenResponse = (new FacebookGraphAuth($guzzleClient))->getLongLivedClientAccesstoken(
                    $this->getAppId(),
                    $this->getAppSecret(),
                    $this->getRedirectUrl(),
                    $this->getLongLivedUserAccessToken()
                );
                $this->setLongLivedClientAccesstoken($tokenResponse['access_token']);
            }
        }

        $token = $this->getTokenFromSample($tokenSample);

        if ($token) {
            $this->setToken($token);
        }
    }

    /**
     * Get the token string for a given sample.
     *
     * @param TokenSample $tokenSample
     * @return string
     */
    public function getTokenFromSample(TokenSample $tokenSample): string
    {
        return match ($tokenSample) {
            TokenSample::USER => (string) ($this->getLongLivedUserAccessToken() ?: $this->getUserAccessToken()),
            TokenSample::APP => (string) $this->getAppAccessToken(),
            TokenSample::PAGE => (string) ($this->getLongLivedPageAccesstoken() ?: $this->getPageAccesstoken() ?: $this->getLongLivedUserAccessToken() ?: $this->getUserAccessToken()),
            TokenSample::CLIENT => (string) ($this->getLongLivedClientAccesstoken() ?: $this->getClientAccesstoken() ?: $this->getLongLivedUserAccessToken() ?: $this->getUserAccessToken()),
        };
    }

    /**
     * Get current user details.
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/user/
     *
     * @param UserPermission[] $permissions
     * @param bool $includeMetadata
     * @return array
     * @throws GuzzleException
     */
    public function getMe(
        array $permissions = [],
        bool $includeMetadata = true
    ): array {
        // Merge fields from provided permissions
        $fields = [];
        foreach ($permissions as $permission) {
            if ($permission instanceof UserPermission) {
                $fields[] = $permission->fields();
            }
        }

        // Use default fields if no permissions are provided
        $fieldsString = !empty($fields) ? implode(',', array_unique(explode(',', implode(',', $fields)))) : 'id,name';

        $query = [
            'fields' => $fieldsString,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: 'me',
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get Facebook Pages where the current user has a role.
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/user/accounts/
     * @note Requires 'pages_show_list' or 'pages_read_engagement' permission.
     *
     * @param PagePermission[] $permissions
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getMyPages(
        array $permissions = [],
        int $limit = 100,
        ?string $fields = null,
    ): array {
        // Merge fields from provided permissions
        $fieldsString = $fields;

        if (!$fieldsString) {
            $permissionFields = [];
            foreach ($permissions as $permission) {
                if ($permission instanceof PagePermission) {
                    $permissionFields[] = $permission->fields();
                }
            }
            // Use default fields if no permissions are provided
            $fieldsString = !empty($permissionFields) ? implode(',', array_unique(explode(',', implode(',', array_filter($permissionFields))))) : 'id,name,access_token';
        }

        $query = [
            'limit' => min($limit, 100),
            'fields' => $fieldsString,
        ];

        $pages = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'me/accounts',
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $pages = [...$pages, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $pages];
    }

    /**
     * Get Facebook Pages for a specific user.
     *
     * @param string $userId
     * @param array $permissions
     * @param int $limit
     * @param string|null $fields
     * @return array
     * @throws GuzzleException
     */
    public function getPages(
        string $userId,
        array $permissions = [],
        int $limit = 100,
        ?string $fields = null,
    ): array {
        // Merge fields from provided permissions
        $fieldsString = $fields;

        if (!$fieldsString) {
            $permissionFields = [];
            foreach ($permissions as $permission) {
                if ($permission instanceof PagePermission) {
                    $permissionFields[] = $permission->fields();
                }
            }
            // Use default fields if no permissions are provided
            $fieldsString = !empty($permissionFields) ? implode(',', array_unique(explode(',', implode(',', array_filter($permissionFields))))) : 'id,name,access_token';
        }

        $query = [
            'limit' => min($limit, 100),
            'fields' => $fieldsString,
        ];

        $pages = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: "{$userId}/accounts",
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $pages = [...$pages, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $pages];
    }

    /**
     * @param PagePermission[] $permissions
     * @param array $pagesIds
     * @return array
     * @throws GuzzleException
     */
    public function getMyPagesByBatch(array $pagesIds, array $permissions = []): array
    {
        // Merge fields from provided permissions
        $fields = [];
        foreach ($permissions as $permission) {
            if ($permission instanceof PagePermission) {
                $fields[] = $permission->fields();
            }
        }

        // Use default fields if no permissions are provided
        $fieldsString = !empty($fields) ? implode(',', array_unique(explode(',', implode(',', array_filter($fields))))) : 'id,name,access_token';

        $query = [
            'batch' => json_encode(array_map(function ($pageId) use ($fieldsString) {
                return [
                    'method' => 'GET',
                    'relative_url' => "{$pageId}?fields={$fieldsString}",
                ];
            }, $pagesIds)),
        ];

        $response = $this->performRequest(
            method: 'POST',
            endpoint: '',
            form_params: $query,
        );

        $results = json_decode($response->getBody()->getContents(), true);
        if (!$results) {
            return [];
        }

        return array_map(function ($item) {
            return json_decode($item['body'] ?? "{}", true);
        }, $results);
    }

    /**
     * Get Ad Accounts available to the current user.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/user/adaccounts/
     * @note Ad Account IDs are usually prefixed with 'act_'.
     *
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getMyAdAccounts(
        int $limit = 100,
        ?string $fields = null,
    ): array {
        $query = [
            'limit' => min($limit, 100),
            'fields' => $fields ?: AdAccountPermission::DEFAULT->fields(),
        ];

        $accounts = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'me/adaccounts',
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $accounts = [...$accounts, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $accounts];
    }

    /**
     * Get Ad Accounts for a specific user.
     *
     * @param string $userId
     * @param int $limit
     * @param string|null $fields
     * @return array
     * @throws GuzzleException
     */
    public function getAdAccounts(
        string $userId,
        int $limit = 100,
        ?string $fields = null,
    ): array {
        $query = [
            'limit' => min($limit, 100),
            'fields' => $fields ?: AdAccountPermission::DEFAULT->fields(),
        ];

        $accounts = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: "{$userId}/adaccounts",
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $accounts = [...$accounts, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $accounts];
    }

    /**
     * Get Ads Pixels for a specific ad account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/adspixels/
     *
     * @param string $adAccountId
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getAdAccountPixels(
        string $adAccountId,
        int $limit = 100,
    ): array {
        $adAccountId = $this->formatAdAccountId($adAccountId);

        $response = $this->performRequest(
            method: 'GET',
            endpoint: '' . $adAccountId . '/adspixels',
            query: [
                'limit' => min($limit, 100),
                'fields' => 'id,name,data_use_setting,creation_time,last_fired_time',
            ],
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get statistics for a specific Ads Pixel.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ads-pixel/stats/
     *
     * @param string $pixelId
     * @param string|null $since
     * @param string|null $until
     * @return array
     * @throws GuzzleException
     */
    public function getAdPixelStats(
        string $pixelId,
        ?string $since = null,
        ?string $until = null,
    ): array {
        $query = [];
        if ($since) {
            $query['since'] = $since;
        }
        if ($until) {
            $query['until'] = $until;
        }

        $response = $this->performRequest(
            method: 'GET',
            endpoint: '' . $pixelId . '/stats',
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Send events to a Facebook Pixel (Conversions API).
     *
     * @see https://developers.facebook.com/docs/marketing-api/conversions-api/
     *
     * @param string $pixelId
     * @param array $events Array of event arrays. Each event should contain 'event_name', 'event_time', 'user_data', etc.
     * @param string|null $testEventCode Optional test event code to verify events in Events Manager.
     * @return array
     * @throws GuzzleException
     */
    public function sendPixelEvents(
        string $pixelId,
        array $events,
        ?string $testEventCode = null,
    ): array {
        $data = [
            'data' => $events,
        ];
        if ($testEventCode) {
            $data['test_event_code'] = $testEventCode;
        }

        $response = $this->performRequest(
            method: 'POST',
            endpoint: '' . $pixelId . '/events',
            body: json_encode($data),
            headers: [
                'Content-Type' => 'application/json',
            ],
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get Custom Audiences for a specific ad account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/customaudiences/
     *
     * @param string $adAccountId
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getAdAccountCustomAudiences(
        string $adAccountId,
        int $limit = 100,
    ): array {
        $adAccountId = $this->formatAdAccountId($adAccountId);

        $response = $this->performRequest(
            method: 'GET',
            endpoint: '' . $adAccountId . '/customaudiences',
            query: [
                'limit' => min($limit, 100),
                'fields' => 'id,name,description,approximate_count_lower_bound,approximate_count_upper_bound,delivery_status,subtype',
            ],
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get delivery status for a specific Custom Audience.
     *
     * @param string $audienceId
     * @return array
     * @throws GuzzleException
     */
    public function getCustomAudienceDeliveryStatus(
        string $audienceId,
    ): array {
        $response = $this->performRequest(
            method: 'GET',
            endpoint: '' . $audienceId,
            query: [
                'fields' => 'delivery_status',
            ],
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Create a Custom Audience for an Ad Account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/customaudiences/#Creating
     *
     * @param string $adAccountId
     * @param string $name
     * @param string $subtype E.g., 'CUSTOM', 'WEBSITE', 'APP', 'OFFLINE', 'CRM'.
     * @param string|null $description
     * @return array
     * @throws GuzzleException
     */
    public function createCustomAudience(
        string $adAccountId,
        string $name,
        string $subtype = 'CUSTOM',
        ?string $description = null,
    ): array {
        $adAccountId = $this->formatAdAccountId($adAccountId);

        $params = [
            'name' => $name,
            'subtype' => $subtype,
            'customer_file_source' => 'USER_PROVIDED_ONLY',
        ];
        if ($description) {
            $params['description'] = $description;
        }

        $response = $this->performRequest(
            method: 'POST',
            endpoint: '' . $adAccountId . '/customaudiences',
            form_params: $params,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Add users to a Custom Audience.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/custom-audience/users/
     *
     * @param string $audienceId
     * @param array $schema List of fields being uploaded (e.g., ['EMAIL', 'PHONE']).
     * @param array $data List of user data arrays, hashed if required by Facebook.
     * @return array
     * @throws GuzzleException
     */
    public function addUsersToCustomAudience(
        string $audienceId,
        array $schema,
        array $data,
    ): array {
        $payload = [
            'payload' => [
                'schema' => $schema,
                'data' => $data,
            ],
        ];

        $response = $this->performRequest(
            method: 'POST',
            endpoint: '' . $audienceId . '/users',
            body: json_encode($payload),
            headers: [
                'Content-Type' => 'application/json',
            ],
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get posts published by a Facebook Page.
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/page/feed/
     * @note This method uses the '/posts' endpoint which only includes posts from the Page itself.
     *
     * @param string $pageId
     * @param string|FacebookPostField[]|string[]|null $postFields
     * @param bool $includeAttachments
     * @param bool $includeComments
     * @param bool $includeReactions
     * @param bool $includeDynamicPosts
     * @param bool $includeSharedPosts
     * @param bool $includeSponsorTags
     * @param bool $includeTo
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getFacebookPosts(
        string $pageId,
        string|array|null $postFields = null, // Comma-separated list of fields
        bool $includeAttachments = false,
        bool $includeComments = false,
        bool $includeReactions = false,
        bool $includeDynamicPosts = false,
        bool $includeSharedPosts = false,
        bool $includeSponsorTags = false,
        bool $includeTo = false,
        int $limit = 10, // Max is 100, but it's limited to 10 by default due to Facebook API limitations
        array $additionalParams = [],
    ): array {

        $fieldsString = null;
        if ($postFields) {
            $fieldsString = is_array($postFields) ?
                implode(',', array_map(fn ($field) => (
                    $field instanceof FacebookPostField ?
                    $field->value :
                    $field
                ), $postFields)) :
                $postFields;
        } else {
            $fieldsString = FacebookPostField::toSafeFieldList($includeDynamicPosts, $includeSharedPosts, $includeSponsorTags, $includeTo);
            if ($includeAttachments) {
                $fieldsString .= ',attachments';
            }
            if ($includeComments) {
                $fieldsString .= ',comments';
            }
            if ($includeReactions) {
                $fieldsString .= ',reactions';
            }
        }

        $query = [
            'fields' => $fieldsString,
            'limit' => min($limit, 100),
        ];

        if (!empty($additionalParams)) {
            $query = array_merge($query, $additionalParams);
        }

        $posts = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: $pageId.'/posts',
                query: $query,
                sleep: $this->sleep,
                tokenSample: TokenSample::PAGE,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $posts = [...$posts, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $posts];
    }

    /**
     * Get ad campaigns for a specific ad account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/campaigns/
     *
     * @param string $adAccountId
     * @param string|CampaignField[]|string[]|null $campaignFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getCampaigns(
        string $adAccountId,
        string|array|null $campaignFields = null, // Comma-separated list of fields
        int $limit = 100,
        array $additionalParams = [],
    ): array {
        $query = [
            'fields' => $campaignFields ?
                (
                    is_array($campaignFields) ?
                    implode(',', array_map(fn ($field) => (
                        $field instanceof CampaignField ?
                        $field->value :
                        $field
                    ), $campaignFields)) :
                    $campaignFields
                ) :
                CampaignField::toCommaSeparatedList(),
            'limit' => min($limit, 100),
        ];

        if (!empty($additionalParams)) {
            $query = array_merge($query, $additionalParams);
        }

        $campaigns = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: $this->formatAdAccountId($adAccountId) . '/campaigns',
                query: $query,
                sleep: $this->sleep,
                tokenSample: TokenSample::USER
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $campaigns = [...$campaigns, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $campaigns];
    }

    /**
     * Get individual ads for a specific ad account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/ads/
     *
     * @param string $adAccountId
     * @param string|AdField[]|string[]|null $adFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getAds(
        string $adAccountId,
        string|array|null $adFields = null, // Comma-separated list of fields
        int $limit = 100,
        array $additionalParams = [],
    ): array {
        $query = [
            'fields' => $adFields ?
                (
                    is_array($adFields) ?
                    implode(',', array_map(fn ($field) => (
                        $field instanceof AdField ?
                        $field->value :
                        $field
                    ), $adFields)) :
                    $adFields
                ) :
                AdField::toCommaSeparatedList(),
            'limit' => min($limit, 100),
        ];

        if (!empty($additionalParams)) {
            $query = array_merge($query, $additionalParams);
        }

        $ads = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: $this->formatAdAccountId($adAccountId) . '/ads',
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $ads = [...$ads, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $ads];
    }

    /**
     * Get ad sets for a specific ad account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/adsets/
     *
     * @param string $adAccountId
     * @param string|AdsetField[]|string[]|null $adsetFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getAdsets(
        string $adAccountId,
        string|array|null $adsetFields = null, // Comma-separated list of fields
        int $limit = 100,
        array $additionalParams = [],
    ): array {
        $query = [
            'fields' => $adsetFields ?
                (
                    is_array($adsetFields) ?
                    implode(',', array_map(fn ($field) => (
                        $field instanceof AdsetField ?
                        $field->value :
                        $field
                    ), $adsetFields)) :
                    $adsetFields
                ) :
                AdsetField::toCommaSeparatedList(),
            'limit' => min($limit, 100),
        ];

        if (!empty($additionalParams)) {
            $query = array_merge($query, $additionalParams);
        }

        $adsets = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: $this->formatAdAccountId($adAccountId) . '/adsets',
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $adsets = [...$adsets, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $adsets];
    }

    /**
     * Get ad creatives for a specific ad account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/adcreatives/
     *
     * @param string $adAccountId
     * @param string|CreativeField[]|string[]|null $creatriveFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getCreatives(
        string $adAccountId,
        string|array|null $creatriveFields = null, // Comma-separated list of fields
        int $limit = 100,
        array $additionalParams = [],
    ): array {
        $query = [
            'fields' => $creatriveFields ?
                (
                    is_array($creatriveFields) ?
                    implode(',', array_map(fn ($field) => (
                        $field instanceof CreativeField ?
                        $field->value :
                        $field
                    ), $creatriveFields)) :
                    $creatriveFields
                ) :
                CreativeField::toCommaSeparatedList(),
            'limit' => min($limit, 100),
        ];

        if (!empty($additionalParams)) {
            $query = array_merge($query, $additionalParams);
        }

        $creatives = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: $this->formatAdAccountId($adAccountId) . '/adcreatives',
                query: $query,
                sleep: $this->sleep,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $creatives = [...$creatives, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $creatives];
    }

    /**
     * Get all Instagram Business account IDs and Pages the user administers.
     *
     * @see https://developers.facebook.com/docs/instagram-api/getting-started/
     * @note Instagram accounts must be linked to a Facebook Page to be returned.
     *
     * @param array $permissions Array of PagePermission enums to specify fields.
     * @param int $limit
     * @return array Array with 'pages' and 'instagram_accounts'.
     * @throws Exception|GuzzleException
     */
    public function getInstagramBusinessAccounts(
        array $permissions = [
            PagePermission::PAGES_SHOW_LIST,
            PagePermission::BUSINESS_MANAGEMENT,
        ],
        int $limit = 100,
    ): array {
        // Combine fields from permissions, ensuring instagram_business_account is included
        $fields = array_unique(array_filter(array_map(
            fn ($perm) => $perm->fields(),
            $permissions
        )));

        $query = [
            'fields' => implode(',', $fields),
            'limit' => min($limit, 100)
        ];

        $pages = [];
        $instagramAccounts = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: 'me/accounts',
                    query: $query,
                    sleep: $this->sleep,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                foreach ($data['data'] ?? [] as $page) {
                    $pages[] = [
                        'page_id' => $page['id'],
                        'page_name' => $page['name'],
                        'is_published' => $page['is_published'] ?? true,
                        'restrictions' => $page['restrictions'] ?? [],
                        'business' => $page['business'] ?? null,
                        'created_by' => $page['created_by'] ?? null,
                        'instagram_business_account' => $page['instagram_business_account']['id'] ?? null
                    ];
                    if (isset($page['instagram_business_account']['id'])) {
                        $instagramAccounts[] = [
                            'page_id' => $page['id'],
                            'page_name' => $page['name'],
                            'instagram_id' => $page['instagram_business_account']['id']
                        ];
                    }
                }

                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            if (empty($pages)) {
                throw new Exception('No Pages found. Verify `pages_show_list` and `business_management` permissions, and user roles.');
            }

            return [
                'pages' => $pages,
                'instagram_accounts' => $instagramAccounts
            ];
        } catch (Exception $e) {
            if (str_contains($e->getMessage(), 'Tried accessing nonexisting field')) {
                throw new Exception('Failed to access fields. Ensure the token has instagram_basic, pages_show_list, and business_management permissions, and Pages are linked to Instagram Business accounts.');
            }
            throw $e;
        }
    }

    /**
     * Get media objects (posts, stories, reels) for an Instagram Business account.
     *
     * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/media/
     *
     * @param string $igUserId The Instagram User ID.
     * @param string|InstagramMediaField[]|string[]|null $mediaFields
     * @param int $limit Number of results per page (max 100).
     * @return array List of media objects.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramMedia(
        string $igUserId,
        string|array|null $mediaFields = null, // Comma-separated list of fields
        int $limit = 100,
        array $additionalParams = [],
    ): array {
        $query = [
            'fields' => $mediaFields ?
                (
                    is_array($mediaFields) ?
                    implode(',', array_map(fn ($field) => (
                        $field instanceof InstagramMediaField ?
                        $field->value :
                        $field
                    ), $mediaFields)) :
                    $mediaFields
                ) :
                InstagramMediaField::toCommaSeparatedList(),
            'limit' => min($limit, 100)
        ];

        if (!empty($additionalParams)) {
            $query = array_merge($query, $additionalParams);
        }

        $media = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: $igUserId."/media",
                    query: $query,
                    sleep: $this->sleep,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $media = array_merge($media, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $media];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve media for Instagram ID ".$igUserId.": ".$e->getMessage());
        }
    }

    /**
     * Alias of getInstagramMedias() to maintain compatibility with ambiguous object names.
     *
     * @param string $igUserId The Instagram User ID.
     * @param string|InstagramMediaField[]|string[]|null $mediaFields
     * @param int $limit Number of results per page (max 100).
     * @return array List of media objects.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramPosts(
        string $igUserId,
        string|array|null $mediaFields = null, // Comma-separated list of fields
        int $limit = 100,
        array $additionalParams = [],
    ): array {
        return $this->getInstagramMedia(
            igUserId: $igUserId,
            mediaFields: $mediaFields,
            limit: $limit,
            additionalParams: $additionalParams,
        );
    }

    /**
     * Get performance metrics for a specific Instagram post, reel, or story.
     *
     * @see https://developers.facebook.com/docs/instagram-api/reference/ig-media/insights/
     * @note Metrics vary by media type (e.g., REELs have 'plays', while IMAGEs don't).
     *
     * @param string $mediaId The Instagram media ID.
     * @param MediaType|MediaProductType $mediaType
     * @param int $limit
     * @return array Insights data.
     * @throws GuzzleException
     */
    public function getInstagramMediaInsights(
        string $mediaId,
        MediaType|MediaProductType $mediaType = MediaType::CAROUSEL_ALBUM,
        int $limit = 100,
        MetricSet $metricSet = MetricSet::BASIC,
        array $customMetrics = [],
    ): array {
        $metrics = $this->resolveMetrics($metricSet, $customMetrics, $mediaType->insightsFields($metricSet));

        $query = [
            'metric' => $metrics,
            'limit' => min($limit, 100),
            'period' => MetricPeriod::LIFETIME->value
        ];

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: $mediaId."/insights",
                    query: $query,
                    sleep: $this->sleep,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for media ID ".$mediaId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for a Facebook Page.
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/page/insights/
     *
     * @param string $pageId
     * @param string|null $since
     * @param string|null $until
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getFacebookPageInsights(
        string $pageId,
        ?string $since = null,
        ?string $until = null,
        MetricSet $metricSet = MetricSet::BASIC,
        array $customMetrics = [],
    ): array {
        $metricsToTry = !empty($customMetrics) ? $customMetrics : explode(',', (string)$this->resolveMetrics($metricSet, $customMetrics, PagePermission::PAGES_SHOW_LIST->insightsFields($metricSet)));

        try {
            $res = $this->executePageInsightsRequest($pageId, $since, $until, $metricsToTry);
            if ($res && !empty($res['data'])) {
                return $res;
            }
            $this->logWarning("First attempt for Page $pageId returned EMPTY data. Switching to incremental search.");
        } catch (Exception $e) {
            if (!$this->isMetricError($e)) throw $e;
            $this->logWarning("First attempt for Page $pageId FAILED with error #100. Switching to incremental search.");
        }

        $results = ['data' => []];
        foreach ($metricsToTry as $metric) {
            try {
                $resSingle = $this->executePageInsightsRequest($pageId, $since, $until, [$metric]);
                if ($resSingle && !empty($resSingle['data'])) {
                    $results['data'] = array_merge($results['data'], $resSingle['data']);
                }
            } catch (Exception $eInner) {
                $this->logError("FB API: Metric '$metric' FAILED for Page $pageId: " . $eInner->getMessage());
            }
        }
        return $results;
    }

    protected function executePageInsightsRequest(string $pageId, ?string $since, ?string $until, array $metrics): array
    {
        $query = [
            'metric' => implode(',', $metrics),
            'period' => MetricPeriod::DAY->value,
            'fields' => 'name,period,values',
        ];

        if ($since) $query['since'] = Carbon::parse($since)->format('Y-m-d');
        if ($until) $query['until'] = Carbon::parse($until)->format('Y-m-d');

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $pageId . "/insights",
            query: $query,
            sleep: $this->sleep,
            tokenSample: TokenSample::PAGE,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    protected function isMetricError(Exception $e): bool
    {
        $msg = $e->getMessage();
        return (stripos($msg, '(#100)') !== false && (stripos($msg, 'insights metric') !== false || stripos($msg, 'param is not valid') !== false));
    }

    protected function logWarning(string $message): void
    {
        if ($this->logger) $this->logger->warning($message);
        else error_log("FB SDK WARNING: $message");
    }

    protected function logError(string $message): void
    {
        if ($this->logger) $this->logger->error($message);
        else error_log("FB SDK ERROR: $message");
    }

    protected function logInfo(string $message): void
    {
        if ($this->logger) $this->logger->info($message);
    }

    /**
     * Get performance insights for a specific Facebook Page post.
     *
     * @see https://developers.facebook.com/docs/graph-api/reference/post/insights/
     *
     * @param string $postId
     * @param int $limit
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getFacebookPostInsights(
        string $postId,
        int $limit = 100,
        MetricSet $metricSet = MetricSet::BASIC,
        array $customMetrics = []
    ): array {

        $metrics = $this->resolveMetrics($metricSet, $customMetrics, FacebookPostPermission::DEFAULT->insightsFields($metricSet));

        $query = [
            'metric' => $metrics,
            'period' => MetricPeriod::LIFETIME->value,
            'limit' => min($limit, 100),
            'fields' => 'name,period,values',
        ];

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: "".$postId."/insights",
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::PAGE,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for media ID ".$postId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for an Ad Account.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/insights/
     *
     * @param string $adAccountId
     * @param int $limit
     * @param MetricBreakdown|array|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAdAccountInsights(
        string $adAccountId,
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        MetricSet $metricSet = MetricSet::BASIC,
        array $additionalParams = [],
        array $customMetrics = [],
    ): array {

        $metrics = $this->resolveMetrics($metricSet, $customMetrics, AdAccountPermission::DEFAULT->insightsFields($metricSet));

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'level' => 'account', 'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if ($metricBreakdown) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } else {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: '' . $this->formatAdAccountId($adAccountId) . '/insights',
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            if (!$this->isMetricError($e)) throw $e;
            $this->logWarning("Ad Account Insights for $adAccountId FAILED with error #100. Returning empty data to allow fallback.");
            return ['data' => []];
        }
    }

    /**
     * Get performance insights for multiple campaigns from the Ad Account Insights endpoint.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/insights/
     * @note Using 'level=campaign' allows retrieving insights for all campaigns in a single request.
     * @note This is much more efficient than fetching insights for each campaign individually.
     *
     * @param string $adAccountId The Ad Account ID.
     * @param array $campaignIds List of Campaign IDs to filter by.
     * @param int $limit Number of results per page (max 100).
     * @param MetricBreakdown|array|null $metricBreakdown Optional breakdown (e.g., age, gender).
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getCampaignInsightsFromAdAccount(
        string $adAccountId,
        array $campaignIds = [],
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        array $additionalParams = [],
        MetricSet $metricSet = MetricSet::BASIC,
        array $customMetrics = [],
    ): array {
        $metrics = $this->resolveMetrics($metricSet, $customMetrics, CampaignPermission::DEFAULT->insightsFields($metricSet));
        if ($metricSet !== MetricSet::CUSTOM) {
             $metrics .= ',campaign_id';
        }

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'level' => 'campaign',
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if (!empty($campaignIds)) {
            $query['filtering'] = json_encode([
                [
                    'field' => 'campaign.id',
                    'operator' => 'IN',
                    'value' => $campaignIds
                ]
            ]);
        }

        if ($metricBreakdown !== null && !empty($metricBreakdown)) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } elseif ($metricBreakdown === null) {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: '' . $this->formatAdAccountId($adAccountId) . '/insights',
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve campaign insights from Ad Account ".$adAccountId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for multiple ad sets from the Ad Account Insights endpoint.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/insights/
     * @note Using 'level=adset' allows retrieving insights for all ad sets in a single request.
     *
     * @param string $adAccountId The Ad Account ID.
     * @param array $adsetIds List of Ad Set IDs to filter by.
     * @param int $limit Number of results per page (max 100).
     * @param MetricBreakdown|array|null $metricBreakdown Optional breakdown (e.g., age, gender).
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAdsetInsightsFromAdAccount(
        string $adAccountId,
        array $adsetIds = [],
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        array $additionalParams = [],
        MetricSet $metricSet = MetricSet::BASIC,
        array $customMetrics = [],
    ): array {
        $metrics = $this->resolveMetrics($metricSet, $customMetrics, AdsetPermission::DEFAULT->insightsFields($metricSet));
        if ($metricSet !== MetricSet::CUSTOM) {
             $metrics .= ',adset_id,campaign_id';
        }

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'level' => 'adset',
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if (!empty($adsetIds)) {
            $query['filtering'] = json_encode([
                [
                    'field' => 'adset.id',
                    'operator' => 'IN',
                    'value' => $adsetIds
                ]
            ]);
        }

        if ($metricBreakdown !== null && !empty($metricBreakdown)) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } elseif ($metricBreakdown === null) {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: '' . $this->formatAdAccountId($adAccountId) . '/insights',
                    query: $query,
                    sleep: 1000000,
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve adset insights from Ad Account ".$adAccountId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for multiple ads from the Ad Account Insights endpoint.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-account/insights/
     * @note Using 'level=ad' allows retrieving insights for all ads in a single request.
     *
     * @param string $adAccountId The Ad Account ID.
     * @param array $adIds List of Ad IDs to filter by.
     * @param int $limit Number of results per page (max 100).
     * @param MetricBreakdown|array|null $metricBreakdown Optional breakdown (e.g., age, gender).
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAdInsightsFromAdAccount(
        string $adAccountId,
        array $adIds = [],
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        array $additionalParams = [],
        MetricSet $metricSet = MetricSet::BASIC,
        array $customMetrics = [],
    ): array {
        $metrics = $this->resolveMetrics($metricSet, $customMetrics, AdPermission::DEFAULT->insightsFields($metricSet));
        if ($metricSet !== MetricSet::CUSTOM) {
             $metrics .= ',ad_id,adset_id,campaign_id';
        }

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'level' => 'ad',
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if (!empty($adIds)) {
            $query['filtering'] = json_encode([
                [
                    'field' => 'ad.id',
                    'operator' => 'IN',
                    'value' => $adIds
                ]
            ]);
        }

        if ($metricBreakdown !== null && !empty($metricBreakdown)) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } elseif ($metricBreakdown === null) {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: '' . $this->formatAdAccountId($adAccountId) . '/insights',
                    query: $query,
                    sleep: 1000000,
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve ad insights from Ad Account ".$adAccountId.": ".$e->getMessage());
        }
    }


    /**
     * Get performance insights for a specific Ad Campaign.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-campaign/insights/
     *
     * @param string $campaignId
     * @param int $limit
     * @param MetricBreakdown|array|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getCampaignInsights(
        string $campaignId,
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        MetricSet $metricSet = MetricSet::BASIC,
        array $additionalParams = [],
        array $customMetrics = [],
    ): array {

        $metrics = $this->resolveMetrics($metricSet, $customMetrics, CampaignPermission::DEFAULT->insightsFields($metricSet));

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if ($metricBreakdown) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } else {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: "".$campaignId."/insights",
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for campaign ID ".$campaignId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for a specific Ad.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/adgroup/insights/
     *
     * @param string $adId
     * @param int $limit
     * @param MetricBreakdown|array|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAdInsights(
        string $adId,
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        MetricSet $metricSet = MetricSet::BASIC,
        array $additionalParams = [],
        array $customMetrics = [],
    ): array {

        $metrics = $this->resolveMetrics($metricSet, $customMetrics, AdPermission::DEFAULT->insightsFields($metricSet));

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if ($metricBreakdown) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } else {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: "".$adId."/insights",
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for ad ID ".$adId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for a specific Ad Set.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-set/insights/
     *
     * @param string $adsetId
     * @param int $limit
     * @param MetricBreakdown|array|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getAdsetInsights(
        string $adsetId,
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        MetricSet $metricSet = MetricSet::BASIC,
        array $additionalParams = [],
        array $customMetrics = [],
    ): array {

        $metrics = $this->resolveMetrics($metricSet, $customMetrics, AdsetPermission::DEFAULT->insightsFields($metricSet));

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if ($metricBreakdown) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } else {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: "".$adsetId."/insights",
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for adset ID ".$adsetId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance insights for an Ad Creative.
     *
     * @see https://developers.facebook.com/docs/marketing-api/reference/ad-creative/
     *
     * @param string $creativeId
     * @param int $limit
     * @param MetricBreakdown|array|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getCreativeInsights(
        string $creativeId,
        int $limit = 100,
        MetricBreakdown|string|array|null $metricBreakdown = null,
        MetricSet $metricSet = MetricSet::BASIC,
        array $additionalParams = [],
        array $customMetrics = [],
    ): array {

        $metrics = $this->resolveMetrics($metricSet, $customMetrics, CreativePermission::DEFAULT->insightsFields($metricSet));

        if ($metricSet !== MetricSet::CUSTOM && $metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = array_merge([
            'fields' => $metrics,
            'limit' => min($limit, 100),
            'time_increment' => 1, // Ensure daily breakdown
            'action_breakdowns' => 'action_type', // Default breakdown for actions
        ], $additionalParams);

        if ($metricBreakdown) {
            $query['breakdowns'] = $this->resolveBreakdowns($metricBreakdown);
        } else {
            $query['breakdowns'] = implode(',', array_map(function ($b) {
                return $b->value;
            }, Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]));
        }

        $insights = [];
        $after = null;

        try {
            do {
                if ($after) {
                    $query['after'] = $after;
                }

                // Get valid metrics from enum
                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: "".$creativeId."/insights",
                    query: $query,
                    sleep: 1000000, // 1 second to avoid rate limiting
                    tokenSample: TokenSample::USER,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for creative ID ".$creativeId.": ".$e->getMessage());
        }
    }

    /**
     * Get performance metrics for an Instagram Business account.
     *
     * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/insights/
     * @note 'since' must be within the last 2 years, and the range ('until' - 'since') cannot exceed 30 days.
     *
     * @param string $instagramAccountId
     * @param string $since
     * @param string $until
     * @param string $timezone
     * @param Metric|Metric[]|null $metrics
     * @param MetricGroup|null $metricGroup
     * @param MetricType|null $metricType
     * @param MetricPeriod|null $metricPeriod
     * @param MetricTimeframe|null $metricTimeframe
     * @param MetricBreakdown|MetricBreakdown[]|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramAccountInsights(
        string $instagramAccountId,
        string $since,
        string $until,
        string $timezone = 'America/Caracas',
        Metric|array|null $metrics = null,
        ?MetricGroup $metricGroup = null,
        ?MetricType $metricType = null,
        ?MetricPeriod $metricPeriod = null,
        ?MetricTimeframe $metricTimeframe = null,
        MetricBreakdown|array|null $metricBreakdown = null,
    ): array {
        $metricsToTry = [];
        if ($metrics) {
            $metricsToTry = is_array($metrics) ? $metrics : [$metrics];
        } elseif ($metricGroup) {
            $metricsToTry = $metricGroup->getMetrics();
        }

        try {
            return $this->executeInstagramAccountInsightsRequest(
                $instagramAccountId, $since, $until, $timezone, $metrics, $metricGroup, $metricType, $metricPeriod, $metricTimeframe, $metricBreakdown
            );
        } catch (Exception $e) {
            if (!$this->isMetricError($e)) throw $e;
            $this->logWarning("IG Account Insights for $instagramAccountId FAILED with error #100. Switching to incremental search.");
        }

        $results = ['data' => []];
        foreach ($metricsToTry as $metric) {
            try {
                $resSingle = $this->executeInstagramAccountInsightsRequest(
                    $instagramAccountId, $since, $until, $timezone, [$metric], null, $metricType, $metricPeriod, $metricTimeframe, $metricBreakdown
                );
                if (!empty($resSingle['data'])) {
                    $results['data'] = array_merge($results['data'], $resSingle['data']);
                }
            } catch (Exception $eInner) {
                $mValue = $metric instanceof Metric ? $metric->value : (string) $metric;
                $this->logError("IG API: Metric '$mValue' FAILED for Account $instagramAccountId: " . $eInner->getMessage());
            }
        }
        return $results;
    }

    protected function executeInstagramAccountInsightsRequest(
        string $instagramAccountId,
        string $since,
        string $until,
        string $timezone = 'America/Caracas',
        Metric|array|null $metrics = null,
        ?MetricGroup $metricGroup = null,
        ?MetricType $metricType = null,
        ?MetricPeriod $metricPeriod = null,
        ?MetricTimeframe $metricTimeframe = null,
        MetricBreakdown|array|null $metricBreakdown = null,
    ): array {
        if (!$metricGroup && !$metrics) {
            throw new InvalidArgumentException('Either `metricGroup` or `metric` must be provided.');
        }

        $query = [
            'fields' => 'name,period,total_value,values,title',
            'since' => $since,
            'until' => $until,
        ];

        if ($metrics) {
            $metricsArray = is_array($metrics) ? $metrics : [$metrics];
            $query['metric'] = implode(',', array_map(fn($m) => $m instanceof Metric ? $m->value : $m, $metricsArray));
        } elseif ($metricGroup) {
            $query['metric'] = implode(',', array_map(fn($e) => $e->value, $metricGroup->getMetrics()));
        }

        if ($metricType) $query['metric_type'] = $metricType->value;
        if ($metricPeriod) $query['period'] = $metricPeriod->value;
        if ($timezone) $query['timezone'] = $timezone;
        if ($metricBreakdown) $query['breakdown'] = is_array($metricBreakdown) ? implode(',', array_map(fn($b) => $b->value, $metricBreakdown)) : $metricBreakdown->value;

        foreach (['metric_timeframe', 'timeframe'] as $key) {
             if ($metricTimeframe) $query[$key] = $metricTimeframe->value;
        }

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $instagramAccountId . "/insights",
            query: $query,
            sleep: $this->sleep,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get daily insights (Reach, Views, etc.) for an Instagram account as Total Values.
     *
     * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/insights/
     *
     * @param string $instagramAccountId
     * @param string $since
     * @param string $timezone
     * @param int $option
     * @return array Insights data.
     * @throws GuzzleException
     */
    public function getDailyInstagramAccountTotalValueInsights(
        string $instagramAccountId,
        string $since,
        string $timezone = 'America/Caracas',
        int $option = 1,
    ): array {
        $metrics = match($option) {
            1 => [Metric::REACH, Metric::VIEWS],
            2 => [Metric::FOLLOWS_AND_UNFOLLOWS],
            3 => [Metric::COMMENTS, Metric::LIKES, Metric::SAVES, Metric::SHARES, Metric::TOTAL_INTERACTIONS],
            4 => [Metric::PROFILE_LINK_TAPS],
            default => [Metric::WEBSITE_CLICKS, Metric::PROFILE_VIEWS, Metric::ACCOUNTS_ENGAGED, Metric::REPLIES, Metric::CONTENT_VIEWS],
        };

        $metricBreakdown = match($option) {
            1 => [MetricBreakdown::MEDIA_PRODUCT_TYPE, MetricBreakdown::FOLLOW_TYPE],
            2 => [MetricBreakdown::FOLLOW_TYPE],
            3 => [MetricBreakdown::MEDIA_PRODUCT_TYPE],
            4 => [MetricBreakdown::CONTACT_BUTTON_TYPE],
            default => [],
        };

        return $this->getInstagramAccountInsights(
            instagramAccountId: $instagramAccountId,
            since: $since,
            until: Carbon::parse($since)->addDay()->format('Y-m-d'),
            timezone: $timezone,
            metrics: $metrics,
            metricType: MetricType::TOTAL_VALUE,
            metricPeriod: MetricPeriod::DAY,
            metricBreakdown: $metricBreakdown,
        );
    }

    /**
     * Get lifetime metrics (mostly Demographics) for an Instagram account as Total Values.
     *
     * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/insights/
     * @note Lifetime metrics do not support specific time ranges but return current accumulated data.
     *
     * @param string $instagramAccountId
     * @param string $since
     * @param string $until
     * @param string $timezone
     * @param Metric|Metric[]|null $metrics
     * @param MetricGroup|null $metricGroup
     * @param MetricBreakdown|MetricBreakdown[]|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getLifetimeInstagramAccountTotalValueInsights(
        string $instagramAccountId,
        string $since,
        string $until,
        string $timezone = 'America/Caracas',
        Metric|array|null $metrics = null,
        ?MetricGroup $metricGroup = null,
        MetricBreakdown|array|null $metricBreakdown = null,
    ): array {
        if (!$metricGroup && empty(array_intersect($metrics, [Metric::FOLLOWER_DEMOGRAPHICS, Metric::REACHED_AUDIENCE_DEMOGRAPHICS, Metric::ENGAGED_AUDIENCE_DEMOGRAPHICS]))) {
            throw new Exception("Invalid metrics or metricGroup provided.");
        }

        return $this->getInstagramAccountInsights(
            instagramAccountId: $instagramAccountId,
            since: $since,
            until: $until,
            timezone: $timezone,
            metrics: $metrics,
            metricGroup: $metricGroup,
            metricType: MetricType::TOTAL_VALUE,
            metricPeriod: MetricPeriod::LIFETIME,
            metricBreakdown: $metricBreakdown,
        );
    }

    /**
     * Get daily Reach metrics for an Instagram account as a Time Series.
     *
     * @see https://developers.facebook.com/docs/instagram-api/reference/ig-user/insights/
     * @note Currently, only the 'reach' metric supports 'time_series'.
     *
     * @param string $instagramAccountId
     * @param string $since
     * @param string $timezone
     * @param MetricGroup|null $metricGroup
     * @return array Insights data.
     * @throws GuzzleException
     */
    public function getDailyInstagramAccountTimeSeriesInsights(
        string $instagramAccountId,
        string $since,
        string $timezone = 'America/Caracas',
        ?MetricGroup $metricGroup = null,
    ): array {
        return $this->getInstagramAccountInsights(
            instagramAccountId: $instagramAccountId,
            since: $since,
            until: Carbon::parse($since)->addDay()->format('Y-m-d'),
            timezone: $timezone,
            metrics: $metricGroup ? null : Metric::REACH,
            metricGroup: $metricGroup,
            metricType: MetricType::TIME_SERIES,
            metricPeriod: MetricPeriod::LIFETIME,
        );
    }

    /**
     * Check if the metric type is valid for the given element (metric or metric group).
     *
     * @param MetricType $metricType
     * @param Metric|MetricGroup|Metric[] $data
     * @return bool
     */
    protected function isValidMetricType(MetricType $metricType, Metric|MetricGroup|array $data): bool
    {
        if ($data instanceof Metric) {
            return in_array($metricType, $data->allowedMetricTypes());
        }

        if ($data instanceof MetricGroup) {
            $metric = $data->getMetrics()[0];
        } else {
            $metric = $data[0];
        }

        return in_array($metricType, $metric->allowedMetricTypes());
    }

    /**
     * Check if the insights' period is valid for the given element (metric or metric group).
     *
     * @param MetricPeriod $metricPeriod
     * @param Metric|MetricGroup|Metric[] $data
     * @return bool
     */
    protected function isValidMetricPeriod(MetricPeriod $metricPeriod, Metric|MetricGroup|array $data): bool
    {
        if ($data instanceof Metric) {
            return in_array($metricPeriod, $data->allowedPeriods());
        }

        if ($data instanceof MetricGroup) {
            $metric = $data->getMetrics()[0];
        } else {
            $metric = $data[0];
        }

        return in_array($metricPeriod, $metric->allowedPeriods());
    }

    /**
     * Check if the insights' timeframe is valid for the given element (metric or metric group).
     *
     * @param MetricTimeframe $metricTimeframe
     * @param Metric|MetricGroup|Metric[] $data
     * @return bool
     */
    protected function isValidMetricTimeframe(MetricTimeframe $metricTimeframe, Metric|MetricGroup|array $data): bool
    {
        if ($data instanceof Metric) {
            return in_array($metricTimeframe, $data->allowedTimeframes());
        }

        if ($data instanceof MetricGroup) {
            $metric = $data->getMetrics()[0];
        } else {
            $metric = $data[0];
        }

        return in_array($metricTimeframe, $metric->allowedMetricTimeframes());
    }

    /**
     * Check if the insights' breakdown is valid for the given element (metric or metric group).
     *
     * @param MetricBreakdown|array $metricBreakdown
     * @param Metric|MetricGroup|Metric[] $data
     * @return bool
     */
    /**
     * @param MetricSet $set
     * @param array $customMetrics
     * @param string $defaultFields
     * @return string
     */
    protected function resolveMetrics(MetricSet $set, array $customMetrics, string $defaultFields): string
    {
        if ($set === MetricSet::CUSTOM) {
            if (empty($customMetrics)) {
                return $defaultFields;
            }
            return implode(',', array_unique(array_map(function ($m) {
                return $m instanceof Metric ? $m->value : $m;
            }, $customMetrics)));
        }
        return $defaultFields;
    }

    /**
     * @param MetricBreakdown|string|array|null $metricBreakdown
     * @return string|null
     */
    protected function resolveBreakdowns(MetricBreakdown|string|array|null $metricBreakdown): ?string
    {
        if (!$metricBreakdown) {
            return null;
        }

        if (is_string($metricBreakdown)) {
            return $metricBreakdown;
        }

        if (is_array($metricBreakdown)) {
            return implode(',', array_map(function ($b) {
                return $b instanceof MetricBreakdown ? $b->value : $b;
            }, $metricBreakdown));
        }

        return $metricBreakdown->value;
    }

    protected function isValidMetricBreakdown(MetricBreakdown|string|array $metricBreakdown, Metric|MetricGroup|array $data): bool
    {
        if (is_string($metricBreakdown)) {
            return true; // We assume custom strings are valid or let the API decide
        }
        $provided = is_array($metricBreakdown) ? $metricBreakdown : [$metricBreakdown];
        $allowedCombinations = [];
        if ($data instanceof Metric) {
            $data = [$data];
        }
        foreach ($data as $item) {
            if ($item instanceof Metric) {
                $allowedCombinations[] = $item->allowedBreakdowns();
            } else {
                $metric = Metric::tryFrom(trim($item));
                if ($metric) {
                    $allowedCombinations[] = $metric->allowedBreakdowns();
                }
            }
        }

        foreach ($allowedCombinations as $allowedCombination) {
            foreach ($allowedCombination as $combo) {
                $providedValues = array_map(fn ($b) => $b instanceof MetricBreakdown ? $b->value : $b, $provided);
                $comboValues = array_map(fn ($b) => $b instanceof MetricBreakdown ? $b->value : $b, $combo);
                if ((count($providedValues) === count($comboValues)) && (array_intersect($providedValues, $comboValues) === $providedValues)) {
                    return true;
                }
            }
        }
        return false;
    }

    protected function formatAdAccountId(string $accountId): string
    {
        return str_starts_with($accountId, 'act_') ? $accountId : 'act_' . $accountId;
    }
}
