<?php

namespace Anibalealvarezs\FacebookGraphApi;

use Anibalealvarezs\ApiSkeleton\Clients\NoAuthClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FacebookGraphAuth extends NoAuthClient
{
    public function __construct(
        ?Client $guzzleClient = null,
    )
    {
        parent::__construct(
            baseUrl: 'https://graph.facebook.com/',
            guzzleClient: $guzzleClient,
        );
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @param string $userAccessToken
     * @return array
     * @throws GuzzleException
     */
    public function getLongLivedUserAccessToken(
        string $clientId,
        string $clientSecret,
        string $userAccessToken,
    ): array {
        $endpoint = 'oauth/access_token';
        $query = [
            'grant_type' => 'fb_exchange_token',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'fb_exchange_token' => $userAccessToken,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $clientId
     * @param string $clientSecret
     * @return array
     * @throws GuzzleException
     */
    public function getAppAccessToken(
        string $clientId,
        string $clientSecret,
    ): array {
        $endpoint = 'oauth/access_token';
        $query = [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $userId
     * @param string $longLivedUserAccessToken
     * @return array
     * @throws GuzzleException
     */
    public function getPageAccessToken(
        string $userId,
        string $longLivedUserAccessToken,
    ): array {
        $endpoint = $userId . '/accounts';
        $query = [
            'access_token' => $longLivedUserAccessToken,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $userId
     * @param string $longLivedUserAccessToken
     * @return array
     * @throws GuzzleException
     */
    public function getLongLivedPageAccessToken(
        string $userId,
        string $longLivedUserAccessToken,
    ): array {
        $endpoint = 'v22.0/' . $userId . '/accounts';
        $query = [
            'access_token' => $longLivedUserAccessToken,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @param string $appId
     * @param string $appSecret
     * @param string $redirectUri
     * @param string $longLivedUserAccessToken
     * @return array
     * @throws GuzzleException
     */
    public function getLongLivedClientAccessToken(
        string $appId,
        string $appSecret,
        string $redirectUri,
        string $longLivedUserAccessToken,
    ): array {
        $endpoint = 'v22.0/oauth/client_code';
        $query = [
            'client_id' => $appId,
            'client_secret' => $appSecret,
            'redirect_uri' => $redirectUri,
            'access_token' => $longLivedUserAccessToken,
        ];

        $response = $this->performRequest(
            method: 'GET',
            endpoint: $endpoint,
            query: $query,
        );

        return json_decode($response->getBody()->getContents(), true);
    }
}