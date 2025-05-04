<?php

namespace Tests\Unit;

use Anibalealvarezs\FacebookGraphApi\Enums\UserFieldsByPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\PageFieldsByPermission;
use Anibalealvarezs\FacebookGraphApi\FacebookGraphApi;
use Anibalealvarezs\FacebookGraphApi\FacebookGraphAuth;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use Exception;
use Faker\Factory as Faker;
use Faker\Generator;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FacebookGraphApiTest extends TestCase
{
    protected Generator $faker;
    protected string $userId;
    protected string $appId;
    protected string $pageId;
    protected string $appSecret;
    protected string $redirectUrl;
    protected string $userAccessToken;
    protected string $longLivedUserAccessToken;
    protected string $appAccessToken;
    protected string $pageAccessToken;
    protected string $longLivedPageAccessToken;
    protected string $clientAccessToken;
    protected string $longLivedClientAccessToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        $this->userId = $this->faker->uuid;
        $this->appId = $this->faker->uuid;
        $this->pageId = $this->faker->uuid;
        $this->appSecret = $this->faker->uuid;
        $this->redirectUrl = 'https://example.com/callback';
        $this->userAccessToken = $this->faker->uuid;
        $this->longLivedUserAccessToken = $this->faker->uuid;
        $this->appAccessToken = $this->faker->uuid;
        $this->pageAccessToken = $this->faker->uuid;
        $this->longLivedPageAccessToken = $this->faker->uuid;
        $this->clientAccessToken = $this->faker->uuid;
        $this->longLivedClientAccessToken = $this->faker->uuid;
    }

    protected function createMockedGuzzleClient(?array $responses = null, ?MockHandler $mock = null): GuzzleClient
    {
        if ($mock === null) {
            $mock = new MockHandler($responses);
        }
        $handler = HandlerStack::create($mock);
        return new GuzzleClient(['handler' => $handler]);
    }

    /**
     * @throws Exception
     */
    public function testConstructorWithValidParameters(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            userAccessToken: $this->userAccessToken,
            longLivedUserAccessToken: $this->longLivedUserAccessToken,
            appAccessToken: $this->appAccessToken,
            pageAccesstoken: $this->pageAccessToken,
            longLivedPageAccesstoken: $this->longLivedPageAccessToken,
            clientAccesstoken: $this->clientAccessToken,
            longLivedClientAccesstoken: $this->longLivedClientAccessToken
        );

        $this->assertEquals('https://graph.facebook.com/', $client->getBaseUrl());
        $this->assertEquals($this->userId, $client->getUserId());
        $this->assertEquals($this->appId, $client->getAppId());
        $this->assertEquals($this->pageId, $client->getPageId());
        $this->assertEquals($this->appSecret, $client->getAppSecret());
        $this->assertEquals($this->redirectUrl, $client->getRedirectUrl());
        $this->assertEquals($this->userAccessToken, $client->getUserAccessToken());
        $this->assertEquals($this->longLivedUserAccessToken, $client->getLongLivedUserAccessToken());
        $this->assertEquals($this->appAccessToken, $client->getAppAccessToken());
        $this->assertEquals($this->pageAccessToken, $client->getPageAccesstoken());
        $this->assertEquals($this->longLivedPageAccessToken, $client->getLongLivedPageAccesstoken());
        $this->assertEquals($this->clientAccessToken, $client->getClientAccesstoken());
        $this->assertEquals($this->longLivedClientAccessToken, $client->getLongLivedClientAccesstoken());
        $this->assertEquals(['location' => 'query', 'name' => 'access_token'], $client->getAuthSettings());
        $this->assertInstanceOf(GuzzleClient::class, $client->getGuzzleClient());
    }

    /**
     * @throws Exception
     */
    public function testConstructorWithEmptyRequiredParameters(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('User ID is required');

        new FacebookGraphApi(
            userId: '',
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId
        );
    }

    /**
     * @throws Exception
     */
    public function testSettersAndGetters(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId
        );

        $newUserId = $this->faker->uuid;
        $client->setUserId($newUserId);
        $this->assertEquals($newUserId, $client->getUserId());

        $newAppId = $this->faker->uuid;
        $client->setAppId($newAppId);
        $this->assertEquals($newAppId, $client->getAppId());

        $newPageId = $this->faker->uuid;
        $client->setPageId($newPageId);
        $this->assertEquals($newPageId, $client->getPageId());

        $newAppSecret = $this->faker->uuid;
        $client->setAppSecret($newAppSecret);
        $this->assertEquals($newAppSecret, $client->getAppSecret());

        $newRedirectUrl = 'https://new.example.com/callback';
        $client->setRedirectUrl($newRedirectUrl);
        $this->assertEquals($newRedirectUrl, $client->getRedirectUrl());

        $newToken = $this->faker->uuid;
        $client->setUserAccessToken($newToken);
        $this->assertEquals($newToken, $client->getUserAccessToken());

        $client->setLongLivedUserAccessToken($newToken);
        $this->assertEquals($newToken, $client->getLongLivedUserAccessToken());

        $client->setAppAccessToken($newToken);
        $this->assertEquals($newToken, $client->getAppAccessToken());

        $client->setPageAccesstoken($newToken);
        $this->assertEquals($newToken, $client->getPageAccesstoken());

        $client->setLongLivedPageAccesstoken($newToken);
        $this->assertEquals($newToken, $client->getLongLivedPageAccesstoken());

        $client->setClientAccesstoken($newToken);
        $this->assertEquals($newToken, $client->getClientAccesstoken());

        $client->setLongLivedClientAccesstoken($newToken);
        $this->assertEquals($newToken, $client->getLongLivedClientAccesstoken());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetMeWithUserTokenSuccess(): void
    {
        $responseData = [
            'id' => '123',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'birthday' => '01/01/1990'
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken,
            guzzleClient: $guzzle
        );

        $permissions = [
            UserFieldsByPermission::PUBLIC_PROFILE,
            UserFieldsByPermission::EMAIL,
            UserFieldsByPermission::USER_BIRTHDAY
        ];
        $expectedFields = 'id,name,first_name,last_name,middle_name,picture,link,name_format,third_party_id,updated_time,verified,email,birthday';

        $response = $client->getMe($permissions);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me?fields=' . urlencode($expectedFields),
            (string)$lastRequest->getUri()
        );
        $this->assertArrayHasKey('Authorization', $lastRequest->getHeaders());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetMeWithDefaultFields(): void
    {
        $responseData = ['id' => '123', 'name' => 'Test User'];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken,
            guzzleClient: $guzzle
        );

        $response = $client->getMe();
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me?fields=id%2Cname',
            (string)$lastRequest->getUri()
        );
        $this->assertArrayHasKey('Authorization', $lastRequest->getHeaders());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetMeFetchesLongLivedUserToken(): void
    {
        $tokenResponse = ['access_token' => $this->longLivedUserAccessToken];
        $responseData = ['id' => '123', 'name' => 'Test User'];
        $mock = new MockHandler([
            new Response(200, [], json_encode($tokenResponse)), // Token fetch response
            new Response(200, [], json_encode($responseData)), // getMe response
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);

        $authMock = $this->createMock(FacebookGraphAuth::class);
        $authMock->method('getLongLivedUserAccessToken')
            ->willReturn($tokenResponse);

        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            userAccessToken: $this->userAccessToken,
            guzzleClient: $guzzle,
            auth: $authMock
        );

        $response = $client->getMe();
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me?fields=id%2Cname',
            (string)$lastRequest->getUri()
        );
        $this->assertArrayHasKey('Authorization', $lastRequest->getHeaders());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetMyPagesWithUserTokenSuccess(): void
    {
        $responseData = [
            'data' => [
                [
                    'id' => '1',
                    'name' => 'Page 1',
                    'access_token' => 'page_token_1',
                    'category' => 'Business',
                    'fan_count' => 15000
                ],
                [
                    'id' => '2',
                    'name' => 'Page 2',
                    'access_token' => 'page_token_2',
                    'category' => 'Community',
                    'fan_count' => 5000
                ]
            ]
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken,
            guzzleClient: $guzzle
        );

        $permissions = [
            PageFieldsByPermission::PAGES_SHOW_LIST,
            PageFieldsByPermission::PAGES_READ_ENGAGEMENT
        ];
        $expectedFields = 'id,name,access_token,category,tasks,is_published,username,is_verified,about,description,fan_count,cover,location,phone,website,email,hours,is_permanently_closed,verification_status,business,engagement,followers_count,new_like_count,rating_count,overall_star_rating,affiliation,company_overview,contact_address,founded,general_info,mission,products';

        $response = $client->getMyPages($permissions);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me/accounts?fields=' . urlencode($expectedFields),
            (string)$lastRequest->getUri()
        );
        $this->assertArrayHasKey('Authorization', $lastRequest->getHeaders());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetMyPagesWithDefaultFields(): void
    {
        $responseData = [
            'data' => [
                [
                    'id' => '1',
                    'name' => 'Page 1',
                    'access_token' => 'page_token_1'
                ],
                [
                    'id' => '2',
                    'name' => 'Page 2',
                    'access_token' => 'page_token_2'
                ]
            ]
        ];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken,
            guzzleClient: $guzzle
        );

        $response = $client->getMyPages();
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me/accounts?fields=id%2Cname%2Caccess_token',
            (string)$lastRequest->getUri()
        );
        $this->assertArrayHasKey('Authorization', $lastRequest->getHeaders());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGuzzleExceptionHandling(): void
    {
        $mock = new MockHandler([
            new RequestException('API error', new Request('GET', 'v22.0/me')),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken,
            guzzleClient: $guzzle
        );

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('API error');

        $client->getMe();
    }
}
