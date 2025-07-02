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
use Anibalealvarezs\FacebookGraphApi\Enums\UserPermission;
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
     * @param UserPermission[] $permissions
     * @param bool $includeMetadata
     * @return array
     * @throws GuzzleException
     */
    public function getMe(
        array $permissions = [],
        bool $includeMetadata = true
    ): array
    {
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

        if ($includeMetadata) {
            $query['metadata'] = '1';
        }

        $response = $this->performRequest(
            method: 'GET',
            endpoint: 'v22.0/me',
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param PagePermission[] $permissions
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getMyPages(
        array $permissions = [],
        int $limit = 1000,
    ): array {
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
            'limit' => min($limit, 1000),
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
                endpoint: 'v22.0/me/accounts',
                query: $query,
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
                    'relative_url' => '/v22.0/' . $pageId,
                    'body' => [
                        'fields' => $fieldsString,
                    ],
                ];
            }, $pagesIds)),
            'include_headers' => false,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: 'v22.0/me/accounts',
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getMyAdAccounts(
        int $limit = 1000,
    ): array {
        $query = [
            'limit' => min($limit, 1000),
            'fields' => AdAccountPermission::DEFAULT->fields(),
        ];

        $accounts = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'v22.0/me/adaccounts',
                query: $query,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $accounts = [...$accounts, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $accounts];
    }

    /**
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
    ): array {
        $query = [
            'fields' => $postFields ?
                (is_array($postFields) ?
                    implode(',', array_map(fn ($field) => (
                    $field instanceof FacebookPostField ?
                        $field->value :
                        $field
                    ), $postFields)) :
                    $postFields
                ) :
                FacebookPostField::toCommaSeparatedList()
                . ($includeAttachments ? ',attachments' : '')
                . ($includeComments ? ',comments' : '')
                . ($includeReactions ? ',reactions' : '')
                . ($includeDynamicPosts ? ',dynamic_posts' : '')
                . ($includeSharedPosts ? ',sharedposts' : '')
                . ($includeSponsorTags ? ',sponsor_tags' : '')
                . ($includeTo ? ',to' : ''),
            'limit' => min($limit, 100),
        ];

        $posts = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'v22.0/'.$pageId.'/posts',
                query: $query,
                tokenSample: TokenSample::PAGE
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $posts = [...$posts, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $posts];
    }

    /**
     * @param string $adAccountId
     * @param string|CampaignField[]|string[]|null $campaignFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getCampaigns(
        string $adAccountId,
        string|array|null $campaignFields = null, // Comma-separated list of fields
        int $limit = 1000,
    ): array {
        $query = [
            'fields' => $campaignFields ?
                (is_array($campaignFields) ?
                    implode(',', array_map(fn ($field) => (
                    $field instanceof CampaignField ?
                        $field->value :
                        $field
                    ), $campaignFields)) :
                    $campaignFields
                ) :
                CampaignField::toCommaSeparatedList(),
            'limit' => min($limit, 1000),
        ];

        $campaigns = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'v22.0/' . $this->formatAdAccountId($adAccountId) . '/campaigns',
                query: $query,
                tokenSample: TokenSample::PAGE
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $campaigns = [...$campaigns, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $campaigns];
    }

    /**
     * @param string $adAccountId
     * @param string|AdField[]|string[]|null $adFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getAds(
        string $adAccountId,
        string|array|null $adFields = null, // Comma-separated list of fields
        int $limit = 1000,
    ): array {
        $query = [
            'fields' => $adFields ?
                (is_array($adFields) ?
                    implode(',', array_map(fn ($field) => (
                    $field instanceof AdField ?
                        $field->value :
                        $field
                    ), $adFields)) :
                    $adFields
                ) :
                AdField::toCommaSeparatedList(),
            'limit' => min($limit, 1000),
        ];

        $ads = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'v22.0/' . $this->formatAdAccountId($adAccountId) . '/ads',
                query: $query,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $ads = [...$ads, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $ads];
    }

    /**
     * @param string $adAccountId
     * @param string|AdsetField[]|string[]|null $adsetFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getAdsets(
        string $adAccountId,
        string|array|null $adsetFields = null, // Comma-separated list of fields
        int $limit = 1000,
    ): array {
        $query = [
            'fields' => $adsetFields ?
                (is_array($adsetFields) ?
                    implode(',', array_map(fn ($field) => (
                    $field instanceof AdsetField ?
                        $field->value :
                        $field
                    ), $adsetFields)) :
                    $adsetFields
                ) :
                AdsetField::toCommaSeparatedList(),
            'limit' => min($limit, 1000),
        ];

        $adsets = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'v22.0/' . $this->formatAdAccountId($adAccountId) . '/adsets',
                query: $query,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $adsets = [...$adsets, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $adsets];
    }

    /**
     * @param string $adAccountId
     * @param string|CreativeField[]|string[]|null $creatriveFields
     * @param int $limit
     * @return array
     * @throws GuzzleException
     */
    public function getCreatives(
        string $adAccountId,
        string|array|null $creatriveFields = null, // Comma-separated list of fields
        int $limit = 1000,
    ): array {
        $query = [
            'fields' => $creatriveFields ?
                (is_array($creatriveFields) ?
                    implode(',', array_map(fn ($field) => (
                    $field instanceof CreativeField ?
                        $field->value :
                        $field
                    ), $creatriveFields)) :
                    $creatriveFields
                ) :
                CreativeField::toCommaSeparatedList(),
            'limit' => min($limit, 1000),
        ];

        $creatives = [];
        $after = null;

        do {
            if ($after) {
                $query['after'] = $after;
            }

            $response = $this->performRequest(
                method: 'GET',
                endpoint: 'v22.0/' . $this->formatAdAccountId($adAccountId) . '/adcreatives',
                query: $query,
            );

            $data = json_decode($response->getBody()->getContents(), true);

            $creatives = [...$creatives, ...$data['data']];

            $after = $data['paging']['cursors']['after'] ?? null;
        } while ($after && count($data['data']) > 0);

        return ['data' => $creatives];
    }

    /**
     * Get all Instagram Business account IDs and Pages the user administers, maximizing results.
     *
     * @param array $permissions Array of PageFieldsByPermission enums to specify fields.
     * @return array Array with 'pages' (all Pages) and 'instagram_accounts' (Pages with Instagram).
     * @throws Exception|GuzzleException If request fails or no Pages are found.
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
            fn($perm) => $perm->fields(),
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
     * @param string|InstagramMediaField[]|string[]|null $mediaFields
     * @param int $limit Number of results per page (max 100).
     * @return array List of media objects.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramMedia(
        string $igUserId,
        string|array|null $mediaFields = null, // Comma-separated list of fields
        int $limit = 1000
    ): array {
        $query = [
            'fields' => $mediaFields ?
                (is_array($mediaFields) ?
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

        $media = [];
        $after = null;

        try {
            do {
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
        int $limit = 1000
    ): array {
        return $this->getInstagramMedia(
            igUserId: $igUserId,
            mediaFields: $mediaFields,
            limit: $limit
        );
    }

    /**
     * Get insights for a specific Instagram post.
     *
     * @param string $mediaId The Instagram media ID.
     * @param MediaProductType $mediaproductType
     * @param int $limit
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getInstagramMediaInsights(
        string $mediaId,
        MediaProductType $mediaproductType = MediaProductType::FEED,
        int $limit = 1000,
    ): array {
        $query = [
            'metric' => $mediaproductType->insightsFields(),
            'limit' => min($limit, 1000),
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
                    endpoint: "v22.0/".$mediaId."/insights",
                    query: $query,
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
     * Get insights for a specific Instagram post.
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
    ): array {

        $query = [
            'metric' => PagePermission::PAGES_SHOW_LIST->insightsFields(),
            'period' => MetricPeriod::DAY->value,
            'fields' => 'name,period,values',
        ];

        if ($since) {
            $query['since'] = Carbon::parse($since)->format('Y-m-d');
        }
        if ($until) {
            $query['until'] = Carbon::parse($until)->format('Y-m-d');
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
                    endpoint: "v22.0/".$pageId."/insights",
                    query: $query,
                    tokenSample: TokenSample::PAGE,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for media ID ".$pageId.": ".$e->getMessage());
        }
    }

    /**
     * Get insights for a specific Instagram post.
     *
     * @param string $postId
     * @param int $limit
     * @return array Insights data.
     * @throws GuzzleException
     * @throws Exception
     */
    public function getFacebookPostInsights(
        string $postId,
        int $limit = 1000,
    ): array {

        $query = [
            'metric' => FacebookPostPermission::DEFAULT->insightsFields(),
            'period' => MetricPeriod::LIFETIME->value,
            'limit' => min($limit, 1000),
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
                    endpoint: "v22.0/".$postId."/insights",
                    query: $query,
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
     * Get insights for a specific Instagram post.
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
        int $limit = 1000,
        MetricBreakdown|array $metricBreakdown = null,
    ): array {

        $metrics = AdAccountPermission::DEFAULT->insightsFields();

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = [
            'fields' => $metrics,
            'limit' => min($limit, 1000),
            'time_increment' => 1, // Ensure daily breakdown
        ];

        if ($metricBreakdown) {
            $query['breakdown'] = is_array($metricBreakdown) ?
                implode(',', array_map(function($b) {
                    return $b->value;
                }, $metricBreakdown)) :
                $metricBreakdown->value;
        } else {
            $query['breakdown'] = implode(',', Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]);
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
                    endpoint: "v22.0/".$adAccountId."/insights",
                    query: $query,
                    tokenSample: TokenSample::PAGE,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for campaign ID ".$adAccountId.": ".$e->getMessage());
        }
    }

    /**
     * Get insights for a specific Instagram post.
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
        int $limit = 1000,
        MetricBreakdown|array $metricBreakdown = null,
    ): array {

        $metrics = CampaignPermission::DEFAULT->insightsFields();

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = [
            'fields' => $metrics,
            'limit' => min($limit, 1000),
            'time_increment' => 1, // Ensure daily breakdown
        ];

        if ($metricBreakdown) {
            $query['breakdown'] = is_array($metricBreakdown) ?
                implode(',', array_map(function($b) {
                    return $b->value;
                }, $metricBreakdown)) :
                $metricBreakdown->value;
        } else {
            $query['breakdown'] = implode(',', Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]);
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
                    endpoint: "v22.0/".$campaignId."/insights",
                    query: $query,
                    tokenSample: TokenSample::PAGE,
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
     * Get insights for a specific Instagram post.
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
        int $limit = 1000,
        MetricBreakdown|array $metricBreakdown = null,
    ): array {

        $metrics = AdPermission::DEFAULT->insightsFields();

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = [
            'fields' => $metrics,
            'limit' => min($limit, 1000),
            'time_increment' => 1, // Ensure daily breakdown
        ];

        if ($metricBreakdown) {
            $query['breakdown'] = is_array($metricBreakdown) ?
                implode(',', array_map(function($b) {
                    return $b->value;
                }, $metricBreakdown)) :
                $metricBreakdown->value;
        } else {
            $query['breakdown'] = implode(',', Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]);
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
                    endpoint: "v22.0/".$adId."/insights",
                    query: $query,
                    tokenSample: TokenSample::PAGE,
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
     * Get insights for a specific Instagram post.
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
        int $limit = 1000,
        MetricBreakdown|array $metricBreakdown = null,
    ): array {

        $metrics = AdsetPermission::DEFAULT->insightsFields();

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = [
            'fields' => $metrics,
            'limit' => min($limit, 1000),
            'time_increment' => 1, // Ensure daily breakdown
        ];

        if ($metricBreakdown) {
            $query['breakdown'] = is_array($metricBreakdown) ?
                implode(',', array_map(function($b) {
                    return $b->value;
                }, $metricBreakdown)) :
                $metricBreakdown->value;
        } else {
            $query['breakdown'] = implode(',', Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]);
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
                    endpoint: "v22.0/".$adsetId."/insights",
                    query: $query,
                    tokenSample: TokenSample::PAGE,
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
     * Get insights for a specific Instagram post.
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
        int $limit = 1000,
        MetricBreakdown|array $metricBreakdown = null,
    ): array {

        $metrics = CreativePermission::DEFAULT->insightsFields();

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, explode(',', $metrics))) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . $metrics . '.');
        }

        $query = [
            'fields' => $metrics,
            'limit' => min($limit, 1000),
            'time_increment' => 1, // Ensure daily breakdown
        ];

        if ($metricBreakdown) {
            $query['breakdown'] = is_array($metricBreakdown) ?
                implode(',', array_map(function($b) {
                    return $b->value;
                }, $metricBreakdown)) :
                $metricBreakdown->value;
        } else {
            $query['breakdown'] = implode(',', Metric::FOLLOWER_DEMOGRAPHICS->allowedBreakdowns()[0]);
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
                    endpoint: "v22.0/".$creativeId."/insights",
                    query: $query,
                    tokenSample: TokenSample::PAGE,
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
     * Get insights for a specific Instagram account.
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
        string $since, // Max: 2 years ago
        string $until, // Max: 30 days from $since
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

        if ($metricType && !$this->isValidMetricType($metricType, $metrics ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric type provided for ' . ($metrics ? 'metric' : 'metric group') . '.');
        }

        if ($metricPeriod && !$this->isValidMetricPeriod($metricPeriod, $metrics ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric period provided for ' . ($metrics ? 'metric' : 'metric group') . '.');
        }

        if ($metricTimeframe && !$this->isValidMetricTimeframe($metricTimeframe, $metrics ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric timeframe provided for ' . ($metrics ? 'metric' : 'metric group') . '.');
        }

        if ($metricBreakdown && !$this->isValidMetricBreakdown($metricBreakdown, $metrics ?? $metricGroup)) {
            throw new InvalidArgumentException('Invalid metric breakdown provided for ' . ($metrics ? 'metric' : 'metric group') . '.');
        }

        $query = [
            'fields' => 'name,period,total_value',
        ];

        if($metrics) {
            if (!is_array($metrics)) {
                $metrics = [$metrics];
            }
            $metricsArray = [];
            foreach ($metrics as $metric) {
                $metricsArray[] = $metric instanceof Metric ? $metric->value : $metric;
            }
            $query['metric'] = implode(',', $metricsArray);;
        } elseif ($metricGroup) {
            $query['metric'] = implode(',', array_map(fn($e) => $e->value, $metricGroup->getMetrics()));
        }

        if ($metricType) {
            $query['metric_type'] = $metricType->value;
        } else {
            if ($metrics) {
                if ($allowedMetricTypes = $metrics[0]->allowedMetricTypes()) {
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
            if ($metrics) {
                if ($allowedPeriods = $metrics[0]->allowedPeriods()) {
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
            if ($metrics) {
                if ($allowedTimeframes = $metrics[0]->allowedTimeframes()) {
                    $query['timeframe'] = $allowedTimeframes[0]->value;
                }
            } elseif ($metricGroup) {
                if ($allowedTimeframes = $metricGroup->getMetrics()[0]->allowedTimeframes()) {
                    $query['timeframe'] = $allowedTimeframes[0]->value;
                }
            }
        }
        
        if ($metricBreakdown) {
            $query['breakdown'] = is_array($metricBreakdown) ?
                implode(',', array_map(function($b) {
                    return $b->value;
                }, $metricBreakdown)) :
                $metricBreakdown->value;
        } else {
            if ($metrics) {
                $allowedBreakdowns = [];
                foreach($metrics as $metric) {
                    foreach ($metric->allowedBreakdowns() as $breakdown) {
                        foreach ($breakdown as $b) {
                            $allowedBreakdowns[] = $b instanceof MetricBreakdown ? $b->value : $b;
                        }
                    }
                }
                $allowedBreakdowns = array_unique($allowedBreakdowns);
                if ($allowedBreakdowns) {
                    $query['breakdown'] = implode(',', $allowedBreakdowns);
                }
            } elseif ($metricGroup) {
                $allowedBreakdowns = [];
                foreach($metricGroup->getMetrics()[0]->allowedBreakdowns() as $breakdown) {
                    if (is_array($breakdown)) {
                        foreach($breakdown as $b) {
                            $allowedBreakdowns[] = $b instanceof MetricBreakdown ? $b->value : $b;
                        }
                    } else {
                        $allowedBreakdowns[] = $breakdown instanceof MetricBreakdown ? $breakdown->value : $breakdown;
                    }
                }
                $allowedBreakdowns = array_unique($allowedBreakdowns);
                if ($allowedBreakdowns) {
                    $query['breakdown'] = implode(',', $allowedBreakdowns);
                }
            }
        }

        if ($since) {
            $query['since'] = Carbon::parse($since, $timezone)->timestamp;
        }

        if ($until) {
            $query['until'] = Carbon::parse($until, $timezone)->timestamp;
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
                    endpoint: "v22.0/".$instagramAccountId."/insights",
                    query: $query,
                );
                $data = json_decode($response->getBody()->getContents(), true);

                $insights = array_merge($insights, $data['data'] ?? []);
                $after = $data['paging']['cursors']['after'] ?? null;
            } while ($after && count($data['data']) > 0);

            return ['data' => $insights];
        } catch (Exception $e) {
            throw new Exception("Failed to retrieve insights for account ID ".$instagramAccountId.": ".$e->getMessage());
        }
    }

    /**
     * Get daily insights for a specific Instagram account measured in total values.
     *
     * @param string $instagramAccountId
     * @param string $since
     * @param string $until
     * @param string $timezone
     * @param MetricGroup|null $metricGroup
     * @param MetricBreakdown|MetricBreakdown[]|null $metricBreakdown
     * @return array Insights data.
     * @throws GuzzleException
     */
    public function getDailyInstagramAccountTotalValueInsights(
        string $instagramAccountId,
        string $since,
        string $until,
        string $timezone = 'America/Caracas',
        ?MetricGroup $metricGroup = null,
        MetricBreakdown|array|null $metricBreakdown = null,
    ): array
    {
        $metrics = match($metricBreakdown) {
            MetricBreakdown::CONTACT_BUTTON_TYPE => Metric::PROFILE_LINK_TAPS,
            MetricBreakdown::FOLLOW_TYPE => [Metric::FOLLOWS_AND_UNFOLLOWS, Metric::REACH, Metric::VIEWS],
            MetricBreakdown::MEDIA_PRODUCT_TYPE => [Metric::COMMENTS, Metric::LIKES, Metric::SAVES, Metric::REACH, Metric::SHARES, Metric::TOTAL_INTERACTIONS, Metric::VIEWS],
            default => Metric::REACH,
        };

        return $this->getInstagramAccountInsights(
            instagramAccountId: $instagramAccountId,
            since: $since,
            until: $until,
            timezone: $timezone,
            metrics: $metrics,
            metricGroup: $metricGroup,
            metricType: MetricType::TOTAL_VALUE,
            metricPeriod: MetricPeriod::DAY,
            metricBreakdown: $metricBreakdown,
        );
    }

    /**
     * Get lifetime insights for a specific Instagram account measured in total values.
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
    ): array
    {
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
     * Get daily insights for a specific Instagram account measured in time series.
     * Allowed for `reach` metric only.
     *
     * @param string $instagramAccountId
     * @param string $since
     * @param string $until
     * @param string $timezone
     * @param MetricGroup|null $metricGroup
     * @return array Insights data.
     * @throws GuzzleException
     */
    public function getDailyInstagramAccountTimeSeriesInsights(
        string $instagramAccountId,
        string $since,
        string $until,
        string $timezone = 'America/Caracas',
        ?MetricGroup $metricGroup = null,
    ): array
    {
        return $this->getInstagramAccountInsights(
            instagramAccountId: $instagramAccountId,
            since: $since,
            until: $until,
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
    protected function isValidMetricBreakdown(MetricBreakdown|array $metricBreakdown, Metric|MetricGroup|array $data): bool
    {
        $provided = is_array($metricBreakdown) ? $metricBreakdown : [$metricBreakdown];
        $allowedCombinations = [];
        if ($data instanceof Metric) {
            $data = [$data];
        }
        foreach($data as $item) {
            $allowedCombinations[] = $item->allowedBreakdowns();
        }

        foreach ($allowedCombinations as $allowedCombination) {
            foreach($allowedCombination as $combo) {
                $providedValues = array_map(fn($b) => $b instanceof MetricBreakdown ? $b->value : $b, $provided);
                $comboValues = array_map(fn($b) => $b instanceof MetricBreakdown ? $b->value : $b, $combo);
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
