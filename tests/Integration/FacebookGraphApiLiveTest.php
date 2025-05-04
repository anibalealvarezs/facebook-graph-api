<?php

namespace Tests\Integration;

use Anibalealvarezs\FacebookGraphApi\FacebookGraphApi;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class FacebookGraphApiLiveTest extends TestCase
{
    protected FacebookGraphApi $api;
    protected string $userId;
    protected string $appId;
    protected string $appSecret;
    protected string $redirectUrl;
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
        $this->longLivedUserAccessToken = $config['fb_graph_long_lived_user_access_token'];

        $this->api = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            longLivedUserAccessToken: $this->longLivedUserAccessToken
        );
    }

    /**
     * @throws GuzzleException
     */
    public function testGetMe(): void
    {
        $response = $this->api->getMe();
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    /**
     * @throws GuzzleException
     */
    public function testGetMyPages(): void
    {
        $response = $this->api->getMyPages();
        $data = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
    }
}
