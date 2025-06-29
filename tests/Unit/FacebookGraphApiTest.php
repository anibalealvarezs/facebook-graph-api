<?php

namespace Tests\Unit;

use Anibalealvarezs\FacebookGraphApi\Enums\InstagramMediaField;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaProductType;
use Anibalealvarezs\FacebookGraphApi\Enums\Metric;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricBreakdown;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricGroup;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricPeriod;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricTimeframe;
use Anibalealvarezs\FacebookGraphApi\Enums\MetricType;
use Anibalealvarezs\FacebookGraphApi\Enums\UserPermission;
use Anibalealvarezs\FacebookGraphApi\Enums\PagePermission;
use Anibalealvarezs\FacebookGraphApi\FacebookGraphApi;
use Anibalealvarezs\FacebookGraphApi\FacebookGraphAuth;
use Anibalealvarezs\ApiSkeleton\Classes\Exceptions\ApiRequestException;
use Carbon\Carbon;
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
            UserPermission::PUBLIC_PROFILE,
            UserPermission::EMAIL,
            UserPermission::USER_BIRTHDAY
        ];
        $expectedFields = 'id,name,first_name,last_name,middle_name,picture,link,name_format,third_party_id,updated_time,verified,email,birthday';

        $response = $client->getMe($permissions);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me?fields=' . urlencode($expectedFields) . '&metadata=1',
            (string)$lastRequest->getUri()
        );
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
            'https://graph.facebook.com/v22.0/me?fields=id%2Cname&metadata=1',
            (string)$lastRequest->getUri()
        );
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
            'https://graph.facebook.com/v22.0/me?fields=id%2Cname&metadata=1',
            (string)$lastRequest->getUri()
        );
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
            PagePermission::PAGES_SHOW_LIST,
            PagePermission::PAGES_READ_ENGAGEMENT
        ];
        $expectedFields = 'id,name,access_token,category,tasks,is_published,username,is_verified,about,description,fan_count,cover,location,phone,website,email,hours,is_permanently_closed,verification_status,business,engagement,followers_count,new_like_count,rating_count,overall_star_rating,affiliation,company_overview,contact_address,founded,general_info,mission,products';

        $limit = Faker::create()->numberBetween(1, 1000);
        $response = $client->getMyPages(permissions: $permissions, limit: $limit);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me/accounts?limit='.$limit.'&fields=' . urlencode($expectedFields),
            (string)$lastRequest->getUri()
        );
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

        $limit = Faker::create()->numberBetween(1, 1000);
        $response = $client->getMyPages(limit: $limit);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/me/accounts?limit='.$limit.'&fields=id%2Cname%2Caccess_token',
            (string)$lastRequest->getUri()
        );
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

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramBusinessAccountsWithBusinessManagement(): void
    {
        $responseData = [
            'data' => [
                [
                    'id' => 'page123',
                    'name' => 'Business Page 1',
                    'is_published' => true,
                    'restrictions' => [],
                    'business' => ['id' => 'biz123', 'name' => 'Business Inc'],
                    'created_by' => 'user123',
                    'instagram_business_account' => ['id' => '17841412345678901']
                ],
                [
                    'id' => 'page456',
                    'name' => 'Business Page 2',
                    'is_published' => true,
                    'restrictions' => [],
                    'business' => ['id' => 'biz456', 'name' => 'Other Inc'],
                    'created_by' => 'user456'
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

        $result = $client->getInstagramBusinessAccounts();
        $this->assertCount(2, $result['pages']);
        $this->assertCount(1, $result['instagram_accounts']);
        $this->assertEquals('page123', $result['pages'][0]['page_id']);
        $this->assertEquals('Business Page 1', $result['pages'][0]['page_name']);
        $this->assertEquals('17841412345678901', $result['pages'][0]['instagram_business_account']);
        $this->assertEquals('biz123', $result['pages'][0]['business']['id']);
        $this->assertNull($result['pages'][1]['instagram_business_account']);
        $this->assertEquals('page456', $result['pages'][1]['page_id']);
        $this->assertEquals('Business Page 2', $result['pages'][1]['page_name']);
        $this->assertEquals('biz456', $result['pages'][1]['business']['id']);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertStringContainsString('fields=' . urlencode('id,name,access_token,category,tasks,is_published,username,is_verified,business,merchant_settings,attribution_details,created_by,created_time,updated_by,updated_time,instagram_business_account') . '&limit=100', (string)$lastRequest->getUri());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramMediaSuccess(): void
    {
        $responseData = [
            'data' => [
                [
                    'id' => '17912345678901234',
                    'media_type' => 'IMAGE',
                    'permalink' => 'https://www.instagram.com/p/ABC123/',
                    'timestamp' => '2025-05-01T12:00:00+0000',
                    'caption' => 'Test post'
                ]
            ],
            'paging' => ['cursors' => ['after' => null]]
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

        $media = $client->getInstagramMedia('17841412345678901');
        $this->assertCount(1, $media);
        $this->assertEquals('17912345678901234', $media['data'][0]['id']);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertStringContainsString('fields=' . urlencode(InstagramMediaField::toCommaSeparatedList()) . '&limit=100', (string)$lastRequest->getUri());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramMediaInsightsSuccess(): void
    {
        $responseData = [
            'data' => [
                [
                    'name' => 'likes',
                    'period' => 'lifetime',
                    'values' => [['value' => 200]],
                    'title' => 'Likes'
                ],
                [
                    'name' => 'comments',
                    'period' => 'lifetime',
                    'values' => [['value' => 50]],
                    'title' => 'Comments'
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

        $insights = $client->getInstagramMediaInsights('17912345678901234');
        $this->assertCount(2, $insights['data']);
        $this->assertEquals('likes', $insights['data'][0]['name']);
        $this->assertEquals(200, $insights['data'][0]['values'][0]['value']);
        $this->assertEquals('comments', $insights['data'][1]['name']);
        $this->assertEquals(50, $insights['data'][1]['values'][0]['value']);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertStringContainsString('metric=' . urlencode('comments,follows,likes,profile_activity,profile_visits,reach,saved,shares,total_interactions,views'), (string)$lastRequest->getUri());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramMediaInsightsFailure(): void
    {
        $mock = new MockHandler([
            new RequestException('API error', new Request('GET', 'v22.0/17912345678901234/insights')),
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

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to retrieve insights for media ID 17912345678901234: API error');

        $client->getInstagramMediaInsights(
            '17912345678901234'
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsWithMetricSuccess(): void
    {
        $responseData = [
            'data' => [
                [
                    'name' => 'reach',
                    'period' => 'day',
                    'values' => [
                        ['value' => 1000, 'end_time' => '2025-05-01T07:00:00+0000'],
                        ['value' => 1200, 'end_time' => '2025-05-02T07:00:00+0000']
                    ],
                    'title' => 'Reach'
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

        $accountId = '17841412345678901';
        $since = '2025-05-01';
        $until = '2025-05-02';

        $insights = $client->getInstagramAccountInsights(
            $accountId,
            $since,
            $until,
            'America/Caracas',
            Metric::REACH,
            null,
            MetricType::TIME_SERIES,
            MetricPeriod::DAY
        );

        $this->assertEquals($responseData['data'], $insights['data']);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $expectedQuery = http_build_query([
            'metric' => 'reach',
            'metric_type' => 'time_series',
            'period' => 'day',
            'breakdown' => 'media_product_type,follow_type',
            'since' => Carbon::parse('2025-05-01', 'America/Caracas')->timestamp,
            'until' => Carbon::parse('2025-05-02', 'America/Caracas')->timestamp
        ]);
        $this->assertStringContainsString($expectedQuery, (string)$lastRequest->getUri());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsWithMetricGroupSuccess(): void
    {
        $responseData = [
            'data' => [
                [
                    'name' => 'reach',
                    'period' => 'day',
                    'values' => [
                        ['value' => 1000, 'end_time' => '2025-05-01T07:00:00+0000'],
                        ['value' => 1200, 'end_time' => '2025-05-02T07:00:00+0000']
                    ],
                    'title' => 'Reach'
                ],
                [
                    'name' => 'follower_count',
                    'period' => 'day',
                    'values' => [
                        ['value' => 500, 'end_time' => '2025-05-01T07:00:00+0000'],
                        ['value' => 510, 'end_time' => '2025-05-02T07:00:00+0000']
                    ],
                    'title' => 'Follower Count'
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

        $accountId = '17841412345678901';
        $since = '2025-05-01';
        $until = '2025-05-02';

        $insights = $client->getInstagramAccountInsights(
            $accountId,
            $since,
            $until,
            'America/Caracas',
            null,
            MetricGroup::REACH_FOLLOWERS,
            MetricType::TIME_SERIES,
            MetricPeriod::DAY
        );

        $this->assertEquals($responseData['data'], $insights['data']);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $expectedQuery = http_build_query([
            'metric' => 'reach,follower_count',
            'metric_type' => 'time_series',
            'period' => 'day',
            'breakdown' => 'media_product_type,follow_type',
            'since' => Carbon::parse('2025-05-01', 'America/Caracas')->timestamp,
            'until' => Carbon::parse('2025-05-02', 'America/Caracas')->timestamp
        ]);
        $this->assertStringContainsString($expectedQuery, (string)$lastRequest->getUri());
        $this->assertEquals('Bearer ' . $this->longLivedUserAccessToken, $lastRequest->getHeaderLine('Authorization'));
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsMissingMetricAndGroup(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Either `metricGroup` or `metric` must be provided.');

        $client->getInstagramAccountInsights(
            '17841412345678901',
            '2025-05-01',
            '2025-05-02'
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsInvalidMetricType(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid metric type provided for metric.');

        $client->getInstagramAccountInsights(
            '17841412345678901',
            '2025-05-01',
            '2025-05-02',
            'America/Caracas',
            Metric::FOLLOWER_COUNT,
            null,
            MetricType::TOTAL_VALUE // Invalid, FOLLOWER_COUNT requires TIME_SERIES
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsInvalidPeriod(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid metric period provided for metric.');

        $client->getInstagramAccountInsights(
            '17841412345678901',
            '2025-05-01',
            '2025-05-02',
            'America/Caracas',
            Metric::TOTAL_INTERACTIONS,
            null,
            null,
            MetricPeriod::WEEK // Invalid, TOTAL_INTERACTIONS allows only DAY
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsInvalidTimeframe(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid metric timeframe provided for metric.');

        $client->getInstagramAccountInsights(
            '17841412345678901',
            '2025-05-01',
            '2025-05-02',
            'America/Caracas',
            Metric::REACH,
            null,
            null,
            null,
            MetricTimeframe::THIS_MONTH // Invalid, REACH has no timeframes
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsInvalidBreakdown(): void
    {
        $client = new FacebookGraphApi(
            userId: $this->userId,
            appId: $this->appId,
            appSecret: $this->appSecret,
            redirectUrl: $this->redirectUrl,
            pageId: $this->pageId,
            longLivedUserAccessToken: $this->longLivedUserAccessToken
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid metric breakdown provided for metric.');

        $client->getInstagramAccountInsights(
            '17841412345678901',
            '2025-05-01',
            '2025-05-02',
            'America/Caracas',
            Metric::REACH,
            null,
            null,
            null,
            null,
            MetricBreakdown::AGE // Invalid, REACH has no breakdowns
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetInstagramAccountInsightsFailure(): void
    {
        $mock = new MockHandler([
            new RequestException('API error', new Request('GET', 'v22.0/17841412345678901/insights')),
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

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to retrieve insights for account ID 17841412345678901: API error');

        $client->getInstagramAccountInsights(
            '17841412345678901',
            '2025-05-01',
            '2025-05-02',
            'America/Caracas',
            Metric::REACH,
            null,
            MetricType::TIME_SERIES,
            MetricPeriod::DAY
        );
    }
}
