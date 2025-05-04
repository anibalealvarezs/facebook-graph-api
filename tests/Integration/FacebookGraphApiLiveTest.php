<?php

namespace Tests\Integration;

use Anibalealvarezs\FacebookGraphApi\Enums\UserFieldsByPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\PageFieldsByPermission;
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
        $permissions = [
            UserFieldsByPermission::PUBLIC_PROFILE,
            UserFieldsByPermission::EMAIL,
        ];
        $data = $this->api->getMe($permissions);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        // Email may be null if not shared, so we check if it's present
        if (isset($data['email'])) {
            $this->assertIsString($data['email']);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetMyPages(): void
    {
        $permissions = [
            PageFieldsByPermission::PAGES_SHOW_LIST,
            // PageFieldsByPermission::PAGES_READ_ENGAGEMENT
        ];
        $data = $this->api->getMyPages($permissions);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('data', $data);
        if (!empty($data['data'])) {
            $page = $data['data'][0];
            $this->assertArrayHasKey('id', $page);
            $this->assertArrayHasKey('name', $page);
            $this->assertArrayHasKey('access_token', $page);
            // Fan count may be present if pages_read_engagement is granted
            if (isset($page['fan_count'])) {
                $this->assertIsInt($page['fan_count']);
            }
        }
    }
}
