<?php

namespace Anibalealvarezs\FacebookGraphApi;

use Anibalealvarezs\ApiSkeleton\Clients\BearerTokenClient;
use Anibalealvarezs\FacebookGraphApi\Enums\InstagramInsightMetricsByMediaType;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaField;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaProductType;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaType;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricGroup;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\Metric;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;
use Anibalealvarezs\FacebookGraphApi\Enums\PageFieldsByPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\TokenSample;
use Anibalealvarezs\FacebookGraphApi\Enums\UserFieldsByPermission;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;

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
    protected ?FacebookGraphAuth $auth;

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
        ?FacebookGraphAuth $auth = null
    ) {
        parent::__construct(
            baseUrl: 'https://graph.facebook.com/',
            token: 'placeholder',
            authSettings: [
                'location' => 'query',
                'name' => 'access_token',
            ],
            guzzleClient: $guzzleClient,
        );

        if (!$userId) {
            throw new InvalidArgumentException('User ID is required');
        }
        $this->setUserId($userId);
        $this->setAppId($appId);
        $this->setPageId($pageId);
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
    }

    public function getAppAccessToken(): ?string
    {
        return $this->appAccessToken;
    }

    public function setAppAccessToken(?string $appAccessToken): void
    {
        $this->appAccessToken = $appAccessToken;
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
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $query
     * @param array|string $body
     * @param array $form_params
     * @param string $baseUrl
     * @param array $headers
     * @param array $additionalHeaders
     * @param CookieJar|null $cookies
     * @param bool $verify
     * @param bool $allowNewToken
     * @param string $pathToSave
     * @param bool|null $stream
     * @param array|null $errorMessageNesting
     * @param int $sleep
     * @param array $customErrors
     * @param bool $ignoreAuth
     * @param TokenSample $tokenSample
     * @return Response
     * @throws GuzzleException
     * @throws Exception
     */
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
        bool $stream = null,
        ?array $errorMessageNesting = null,
        int $sleep = 0,
        array $customErrors = [],
        bool $ignoreAuth = false,
        TokenSample $tokenSample = TokenSample::USER,
    ): Response {

        $this->setSampleBasedToken($tokenSample);

        return parent::performRequest(
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
            ignoreAuth: $ignoreAuth
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    private function setSampleBasedToken(TokenSample $tokenSample): void
    {
        $guzzleClient = $this->guzzleClient;

        if ($tokenSample === TokenSample::USER) {
            if (!$this->getLongLivedUserAccessToken() || ($this->getLongLivedUserAccessToken() === 'placeholder')) {
                $tokenResponse = (new FacebookGraphAuth($guzzleClient))->getLongLivedUserAccessToken(
                    $this->getAppId(),
                    $this->getAppSecret(),
                    $this->getUserAccessToken(),
                );
                $this->setLongLivedUserAccessToken($tokenResponse['access_token']);
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
                $tokenResponse = (new FacebookGraphAuth($guzzleClient))->getLongLivedPageAccesstoken(
                    $this->getUserId(),
                    $this->getLongLivedUserAccessToken(),
                );
                $page = array_filter(
                    $tokenResponse['data'],
                    fn($page) => $page['id'] === $this->getPageId()
                );
                if (empty($page)) {
                    throw new Exception('Page not found');
                }
                $this->setLongLivedPageAccesstoken($page[0]['access_token']);
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

        $this->setToken(
            match($tokenSample) {
                TokenSample::USER => $this->getLongLivedUserAccessToken(),
                TokenSample::APP => $this->getAppAccessToken(),
                TokenSample::PAGE => $this->getLongLivedPageAccesstoken(),
                TokenSample::CLIENT => $this->getLongLivedClientAccesstoken(),
            }
        );
    }

    /**
     * @param UserFieldsByPermission[] $permissions
     * @return array
     * @throws GuzzleException
     */
    public function getMe(
        array $permissions = [],
    ): array
    {
        $endpoint = 'v22.0/me';

        // Merge fields from provided permissions
        $fields = [];
        foreach ($permissions as $permission) {
            if ($permission instanceof UserFieldsByPermission) {
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
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param PageFieldsByPermission[] $permissions
     * @return array
     * @throws GuzzleException
     */
    public function getMyPages(array $permissions = []): array
    {
        $endpoint = 'v22.0/me/accounts';

        // Merge fields from provided permissions
        $fields = [];
        foreach ($permissions as $permission) {
            if ($permission instanceof PageFieldsByPermission) {
                $fields[] = $permission->fields();
            }
        }

        // Use default fields if no permissions are provided
        $fieldsString = !empty($fields) ? implode(',', array_unique(explode(',', implode(',', array_filter($fields))))) : 'id,name,access_token';

        $query = [
            'fields' => $fieldsString,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * Get all Instagram Business account IDs and Pages the user administers, maximizing results.
     *
     * @param array $permissions Array of PageFieldsByPermission enums to specify fields.
     * @return array Array with 'pages' (all Pages) and 'instagram_accounts' (Pages with Instagram).
     * @throws Exception|GuzzleException If request fails or no Pages are found.
     */
    public function getInstagramBusinessAccounts(array $permissions = [
        PageFieldsByPermission::PAGES_SHOW_LIST,
        PageFieldsByPermission::BUSINESS_MANAGEMENT
    ]): array
    {
        $pages = [];
        $instagramAccounts = [];
        $after = null;
        $limit = 100;

        // Combine fields from permissions, ensuring instagram_business_account is included
        $fields = array_unique(array_filter(array_map(
            fn($perm) => $perm->fields(),
            $permissions
        )));
        $fieldsString = implode(',', $fields);

        try {
            do {
                $query = [
                    'fields' => $fieldsString,
                    'limit' => $limit
                ];
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: 'v22.0/me/accounts',
                    query: $query,
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
     * Get media IDs for an Instagram Business account.
     *
     * @param string $igUserId The Instagram User ID.
     * @param string|null $mediaFields
     * @param int $limit Number of results per page (max 100).
     * @return array List of media objects.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramMedia(
        string $igUserId,
        ?string $mediaFields = null, // Comma-separated list of fields
        int $limit = 100
    ): array {
        $media = [];
        $after = null;

        try {
            do {
                $query = [
                    'fields' => $mediaFields ?: MediaField::toCommaSeparatedList(),
                    'limit' => min($limit, 100)
                ];
                if ($after) {
                    $query['after'] = $after;
                }

                $response = $this->performRequest(
                    method: 'GET',
                    endpoint: "v22.0/".$igUserId."/media",
                    query: $query
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $media = array_merge($media, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return $media;
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve media for Instagram ID ".$igUserId.": ".$e->getMessage());
        }
    }

    /**
     * Get insights for a specific Instagram post.
     *
     * @param string $mediaId The Instagram media ID.
     * @param MediaProductType $mediaproductType
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramMediaInsights(
        string $mediaId,
        MediaProductType $mediaproductType = MediaProductType::FEED,
    ): array {
        try {
            // Get valid metrics from enum
            $response = $this->performRequest(
                method: 'GET',
                endpoint: "v22.0/".$mediaId."/insights",
                query: ['metric' => $mediaproductType->fields()]
            );
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data'] ?? [];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for media ID ".$mediaId.": ".$e->getMessage());
        }
    }

    /**
     * Get insights for a specific Instagram post.
     *
     * @param string $accountId
     * @param string $since
     * @param string $until
     * @param string $timezone
     * @param Metric|null $metric
     * @param MetricGroup|null $metricGroup
     * @param MetricType|null $metricType
     * @param MetricPeriod|null $metricPeriod
     * @param MetricTimeframe|null $metricTimeframe
     * @param MetricBreakdown|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramAccountInsights(
        string $accountId,
        string $since,
        string $until,
        string $timezone = 'America/Caracas',
        ?Metric $metric = null,
        ?MetricGroup $metricGroup = null,
        ?MetricType $metricType = null,
        ?MetricPeriod $metricPeriod = null,
        ?MetricTimeframe $metricTimeframe = null,
        ?MetricBreakdown $metricBreakdown = null,
    ): array {
        if (!$metricGroup && !$metric) {
            throw new InvalidArgumentException('Either `metricGroup` or `metric` must be provided.');
        }

        if ($metricType && !$this->isValidMetricType($metricType, $metric ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric type provided for ' . ($metric ? 'metric' : 'metric group') . '.');
        }

        if ($metricPeriod && !$this->isValidMetricPeriod($metricPeriod, $metric ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric period provided for ' . ($metric ? 'metric' : 'metric group') . '.');
        }

        if ($metricTimeframe && !$this->isValidMetricTimeframe($metricTimeframe, $metric ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric timeframe provided for ' . ($metric ? 'metric' : 'metric group') . '.');
        }

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, $metric ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . ($metric ? 'metric' : 'metric group') . '.');
        }

        $query = [];

        if($metric) {
            $query['metric'] = $metric->value;
        } elseif ($metricGroup) {
            $query['metric'] = implode(',', array_map(fn($e) => $e->value, $metricGroup->getMetrics()));
        }

        if ($metricType) {
            $query['metric_type'] = $metricType->value;
        } else {
            if ($metric) {
                if ($allowedMetricTypes = $metric->allowedMetricTypes()) {
                    $query['metric_type'] = $allowedMetricTypes[0]->value;
                }
            } elseif ($metricGroup) {
                if ($allowedMetricTypes = $metricGroup->getMetrics()[0]->allowedMetricTypes()) {
                    $query['metric_type'] = $allowedMetricTypes[0]->value;
                }
            }
        }
        
        if ($metricPeriod) {
            $query['period'] = $metricPeriod->value;
        } else {
            if ($metric) {
                if ($allowedPeriods = $metric->allowedPeriods()) {
                    $query['period'] = $allowedPeriods[0]->value;
                }
            } elseif ($metricGroup) {
                if ($allowedPeriods = $metricGroup->getMetrics()[0]->allowedPeriods()) {
                    $query['period'] = $allowedPeriods[0]->value;
                }
            }
        }
        
        if ($metricTimeframe) {
            $query['timeframe'] = $metricTimeframe->value;
        } else {
            if ($metric) {
                if ($allowedTimeframes = $metric->allowedTimeframes()) {
                    $query['timeframe'] = $allowedTimeframes[0]->value;
                }
            } elseif ($metricGroup) {
                if ($allowedTimeframes = $metricGroup->getMetrics()[0]->allowedTimeframes()) {
                    $query['timeframe'] = $allowedTimeframes[0]->value;
                }
            }
        }
        
        if ($metricBreakdown) {
            $query['breakdown'] = $metricBreakdown->value;
        } else {
            if ($metric) {
                if ($allowedBreakdowns = $metric->allowedBreakdowns()) {
                    $query['breakdown'] = $allowedBreakdowns[0]->value;
                }
            } elseif ($metricGroup) {
                if ($allowedBreakdowns = $metricGroup->getMetrics()[0]->allowedBreakdowns()) {
                    $query['breakdown'] = $allowedBreakdowns[0]->value;
                }
            }
        }

        if ($since) {
            $query['since'] = Carbon::parse($since, $timezone)->timestamp;
        }

        if ($until) {
            $query['until'] = Carbon::parse($until, $timezone)->timestamp;
        }

        try {
            // Get valid metrics from enum
            $response = $this->performRequest(
                method: 'GET',
                endpoint: "v22.0/".$accountId."/insights",
                query: $query
            );
            $data = json_decode($response->getBody()->getContents(), true);
            return $data['data'] ?? [];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for account ID ".$accountId.": ".$e->getMessage());
        }
    }

    /**
     * Check if the metric type is valid for the given element (metric or metric group).
     *
     * @param MetricType $metricType
     * @param Metric|MetricGroup $element
     * @return bool
     */
    protected function isValidMetricType(MetricType $metricType, Metric|MetricGroup $element): bool
    {
        if ($element instanceof Metric) {
            return in_array($metricType, $element->allowedMetricTypes());
        }

        return in_array($metricType, $element->getMetrics()[0]->allowedMetricTypes());
    }

    /**
     * Check if the insights' period is valid for the given element (metric or metric group).
     *
     * @param MetricPeriod $metricPeriod
     * @param Metric|MetricGroup $element
     * @return bool
     */
    protected function isValidMetricPeriod(MetricPeriod $metricPeriod, Metric|MetricGroup $element): bool
    {
        if ($element instanceof Metric) {
            return in_array($metricPeriod, $element->allowedPeriods());
        }

        return in_array($metricPeriod, $element->getMetrics()[0]->allowedPeriods());
    }

    /**
     * Check if the insights' timeframe is valid for the given element (metric or metric group).
     *
     * @param MetricTimeframe $metricTimeframe
     * @param Metric|MetricGroup $element
     * @return bool
     */
    protected function isValidMetricTimeframe(MetricTimeframe $metricTimeframe, Metric|MetricGroup $element): bool
    {
        if ($element instanceof Metric) {
            return in_array($metricTimeframe, $element->allowedTimeframes());
        }

        return in_array($metricTimeframe, $element->getMetrics()[0]->allowedMetricTimeframes());
    }

    /**
     * Check if the insights' breakdown is valid for the given element (metric or metric group).
     *
     * @param MetricBreakdown $metricBreakdown
     * @param Metric|MetricGroup $element
     * @return bool
     */
    protected function isValidMetricBreakdown(MetricBreakdown $metricBreakdown, Metric|MetricGroup $element): bool
    {
        if ($element instanceof Metric) {
            return in_array($metricBreakdown, $element->allowedBreakdowns());
        }

        return in_array($metricBreakdown, $element->getMetrics()[0]->allowedMetricBreakdowns());
    }
}
