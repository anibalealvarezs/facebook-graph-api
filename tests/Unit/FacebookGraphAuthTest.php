<?php

namespace Tests\Unit;

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
use PHPUnit\Framework\TestCase;

class FacebookGraphAuthTest extends TestCase
{
    protected Generator $faker;
    protected string $clientId;
    protected string $clientSecret;
    protected string $userAccessToken;
    protected string $userId;
    protected string $appId;
    protected string $appSecret;
    protected string $redirectUri;
    protected string $longLivedUserAccessToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faker = Faker::create();
        $this->clientId = $this->faker->uuid;
        $this->clientSecret = $this->faker->uuid;
        $this->userAccessToken = $this->faker->uuid;
        $this->userId = $this->faker->uuid;
        $this->appId = $this->faker->uuid;
        $this->appSecret = $this->faker->uuid;
        $this->redirectUri = 'https://example.com/callback';
        $this->longLivedUserAccessToken = $this->faker->uuid;
    }

    protected function createMockedGuzzleClient(?array $responses = null, ?MockHandler $mock = null): GuzzleClient
    {
        if ($mock === null) {
            $mock = new MockHandler($responses);
        }
        $handler = HandlerStack::create($mock);
        return new GuzzleClient(['handler' => $handler]);
    }

    public function testConstructor(): void
    {
        $client = new FacebookGraphAuth();

        $this->assertEquals('https://graph.facebook.com/', $client->getBaseUrl());
        $this->assertInstanceOf(GuzzleClient::class, $client->getGuzzleClient());
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetLongLivedUserAccessTokenSuccess(): void
    {
        $responseData = ['access_token' => 'long-lived-token'];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphAuth(
            guzzleClient: $guzzle,
        );

        $response = $client->getLongLivedUserAccessToken($this->clientId, $this->clientSecret, $this->userAccessToken);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/oauth/access_token?' .
            'grant_type=fb_exchange_token&client_id=' . $this->clientId .
            '&client_secret=' . $this->clientSecret . '&fb_exchange_token=' . $this->userAccessToken,
            (string)$lastRequest->getUri()
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetAppAccessTokenSuccess(): void
    {
        $responseData = ['access_token' => 'app-token'];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphAuth(
            guzzleClient: $guzzle,
        );

        $response = $client->getAppAccessToken($this->clientId, $this->clientSecret);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/oauth/access_token?' .
            'grant_type=client_credentials&client_id=' . $this->clientId .
            '&client_secret=' . $this->clientSecret,
            (string)$lastRequest->getUri()
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetPageAccessTokenSuccess(): void
    {
        $responseData = ['data' => [['id' => 'page1', 'access_token' => 'page-token']]];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphAuth(
            guzzleClient: $guzzle,
        );

        $response = $client->getPageAccessToken($this->userId, $this->longLivedUserAccessToken);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/' . $this->userId . '/accounts?access_token=' . $this->longLivedUserAccessToken,
            (string)$lastRequest->getUri()
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetLongLivedPageAccessTokenSuccess(): void
    {
        $responseData = ['data' => [['id' => 'page1', 'access_token' => 'long-lived-page-token']]];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphAuth(
            guzzleClient: $guzzle,
        );

        $response = $client->getLongLivedPageAccessToken($this->userId, $this->longLivedUserAccessToken);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/' . $this->userId . '/accounts?access_token=' . $this->longLivedUserAccessToken,
            (string)$lastRequest->getUri()
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGetLongLivedClientAccessTokenSuccess(): void
    {
        $responseData = ['code' => 'client-code'];
        $mock = new MockHandler([
            new Response(200, [], json_encode($responseData)),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphAuth(
            guzzleClient: $guzzle,
        );

        $response = $client->getLongLivedClientAccessToken($this->appId, $this->appSecret, $this->redirectUri, $this->longLivedUserAccessToken);
        $this->assertEquals($responseData, $response);
        $lastRequest = $mock->getLastRequest();
        $this->assertEquals('GET', $lastRequest->getMethod());
        $this->assertEquals(
            'https://graph.facebook.com/v22.0/oauth/client_code?' .
            'client_id=' . $this->appId . '&client_secret=' . $this->appSecret .
            '&redirect_uri=' . urlencode($this->redirectUri) . '&access_token=' . $this->longLivedUserAccessToken,
            (string)$lastRequest->getUri()
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function testGuzzleExceptionHandling(): void
    {
        $mock = new MockHandler([
            new RequestException('API error', new Request('GET', 'oauth/access_token')),
        ]);
        $guzzle = $this->createMockedGuzzleClient(mock: $mock);
        $client = new FacebookGraphAuth(
            guzzleClient: $guzzle,
        );

        $this->expectException(ApiRequestException::class);
        $this->expectExceptionMessage('API error');

        $client->getAppAccessToken($this->clientId, $this->clientSecret);
    }
}