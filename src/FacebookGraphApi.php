<?php

namespace Anibalealvarezs\FacebookGraphApi;

use Anibalealvarezs\ApiSkeleton\Clients\BearerTokenClient;
use Anibalealvarezs\FacebookGraphApi\Enums\TokenSample;
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
     * @return Response
     * @throws GuzzleException
     */
    public function getMe(): Response
    {
        $endpoint = 'v22.0/me';

        return $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
        );
    }

    /**
     * @return Response
     * @throws GuzzleException
     */
    public function getMyPages(): Response
    {
        $endpoint = 'v22.0/me/accounts';

        return $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
        );
    }
}
