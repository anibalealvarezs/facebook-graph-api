<?php

namespace Tests\Integration;

use Anibalealvarezs\FacebookGraphApi\FacebookGraphAuth;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class FacebookGraphAuthLiveTest extends TestCase
{
    protected FacebookGraphAuth $auth;
    protected string $userId;
    protected string $appId;
    protected string $appSecret;
    protected string $redirectUrl;
    protected string $userAccessToken;
    protected string $longLivedUserAccessToken;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $config = app_config();

        $this->userId = $config['fb_user_id'];
        $this->appId = $config['fb_app_id'];
        $this->appSecret = $config['fb_app_secret'];
        $this->redirectUrl = $config['fb_app_redirect_uri'];
        $this->userAccessToken = $config['fb_graph_user_access_token'];
        $this->longLivedUserAccessToken = $config['fb_graph_long_lived_user_access_token'];

        $this->auth = new FacebookGraphAuth(new Client());
    }

    /**
     * @group user-token
     * @throws GuzzleException
     */
    public function testGetLongLivedUserAccessToken(): void
    {
        $response = $this->auth->getLongLivedUserAccessToken(
            $this->appId,
            $this->appSecret,
            $this->userAccessToken
        );
        $this->assertIsArray($response);
        $this->assertArrayHasKey('access_token', $response);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetAppAccessToken(): void
    {
        $response = $this->auth->getAppAccessToken(
            $this->appId,
            $this->appSecret
        );
        $this->assertIsArray($response);
        $this->assertArrayHasKey('access_token', $response);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetLongLivedPageAccesstoken(): void
    {
        $response = $this->auth->getLongLivedPageAccesstoken(
            $this->userId,
            $this->longLivedUserAccessToken
        );
        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);
    }

    /**
     * @group client
     * @throws GuzzleException
     */
    public function testGetLongLivedClientAccesstoken(): void
    {
        $response = $this->auth->getLongLivedClientAccesstoken(
            $this->appId,
            $this->appSecret,
            $this->redirectUrl,
            $this->longLivedUserAccessToken
        );
        $this->assertIsArray($response);
        $this->assertArrayHasKey('access_token', $response);
    }

    /**
     * @group user-token
     * @throws GuzzleException
     */
    public function testGetLongLivedUserLongLivedPageAccesstoken(): void
    {
        $longLivedUserToken = $this->auth->getLongLivedUserAccessToken(
            $this->appId,
            $this->appSecret,
            $this->userAccessToken
        )['access_token'];

        $response = $this->auth->getLongLivedPageAccesstoken(
            $this->userId,
            $longLivedUserToken
        );
        $this->assertIsArray($response);
        $this->assertArrayHasKey('data', $response);
    }

    /**
     * @group client
     * @group user-token
     * @throws GuzzleException
     */
    public function testGetLongLivedUserLongLivedClientAccesstoken(): void
    {
        $longLivedUserToken = $this->auth->getLongLivedUserAccessToken(
            $this->appId,
            $this->appSecret,
            $this->userAccessToken
        )['access_token'];

        $response = $this->auth->getLongLivedClientAccesstoken(
            $this->appId,
            $this->appSecret,
            $this->redirectUrl,
            $longLivedUserToken
        );
        $this->assertIsArray($response);
        $this->assertArrayHasKey('access_token', $response);
    }
}
