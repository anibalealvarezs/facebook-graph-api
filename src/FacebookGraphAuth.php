<?php

namespace Anibalealvarezs\FacebookGraphApi;

use Anibalealvarezs\ApiSkeleton\Clients\NoAuthClient;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class FacebookGraphAuth extends NoAuthClient
{
    public function __construct(
        ?Client $guzzleClient = null,
    ) {
        parent::__construct(
            baseUrl: 'https://graph.facebook.com/',
            guzzleClient: $guzzleClient,
        );
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

    /**
     * Exchange a short-lived user access token for a long-lived one (usually valid for 60 days).
     *
     * @see https://developers.facebook.com/docs/facebook-login/guides/access-tokens/get-long-lived/
     *
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
     * Get an App Access Token using client credentials.
     *
     * @see https://developers.facebook.com/docs/facebook-login/guides/access-tokens/#apptokens
     * @note App tokens are used to modify app settings or read insights not tied to a specific user.
     *
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
     * Get Facebook Page Access Tokens for the Pages the user administers.
     *
     * @see https://developers.facebook.com/docs/facebook-login/guides/access-tokens/#pagetokens
     *
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
     * Get long-lived Page Access Tokens using a long-lived User Access Token.
     *
     * @see https://developers.facebook.com/docs/facebook-login/guides/access-tokens/get-long-lived/#get-a-long-lived-page-access-token
     * @note This returns a list of Pages the user has a role on, including their permanent Page Access Tokens.
     *
     * @param string $userId
     * @param string $longLivedUserAccessToken
     * @return array
     * @throws GuzzleException
     */
    public function getLongLivedPageAccessToken(
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
     * Exchange a long-lived User Access Token for a Code to be used on the client side.
     *
     * @see https://developers.facebook.com/docs/facebook-login/guides/access-tokens/get-long-lived/#get-a-long-lived-client-access-token
     *
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
        $endpoint = 'oauth/client_code';
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
