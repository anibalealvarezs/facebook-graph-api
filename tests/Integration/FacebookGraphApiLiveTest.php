<?php

namespace Tests\Integration;

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
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\TestCase;

class FacebookGraphApiLiveTest extends TestCase
{
    protected FacebookGraphApi $api;
    protected string $userId;
    protected string $appId;
    protected string $appSecret;
    protected string $redirectUrl;
    protected string $longLivedUserAccessToken;
    protected Logger $logger;

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

        // Initialize Monolog logger
        $this->logger = new Logger('test');
        $this->logger->pushHandler(new StreamHandler('tests.log', 'debug'));
    }

    /**
     * @throws GuzzleException
     */
    public function testGetMe(): void
    {
        $permissions = [
            UserPermission::PUBLIC_PROFILE,
            UserPermission::EMAIL,
        ];
        $data = $this->api->getMe($permissions);

        $this->logger->debug('testGetMe response', $data);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
        // Email may be null if not shared
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
            PagePermission::PAGES_SHOW_LIST,
            // PageFieldsByPermission::PAGES_READ_ENGAGEMENT
        ];
        $data = $this->api->getMyPages($permissions);

        $this->logger->debug('testGetMyPages response', $data);

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

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramBusinessAccounts(): void
    {
        $permissions = [
            PagePermission::PAGES_SHOW_LIST,
            PagePermission::BUSINESS_MANAGEMENT
        ];
        $data = $this->api->getInstagramBusinessAccounts($permissions);

        $this->logger->debug('testGetInstagramBusinessAccounts response', $data);

        $this->assertIsArray($data);
        $this->assertArrayHasKey('pages', $data);
        $this->assertArrayHasKey('instagram_accounts', $data);
        $this->assertIsArray($data['pages']);
        $this->assertIsArray($data['instagram_accounts']);
        if (!empty($data['pages'])) {
            $page = $data['pages'][0];
            $this->assertArrayHasKey('page_id', $page);
            $this->assertArrayHasKey('page_name', $page);
            $this->assertArrayHasKey('is_published', $page);
            $this->assertArrayHasKey('restrictions', $page);
            $this->assertArrayHasKey('business', $page);
            $this->assertArrayHasKey('created_by', $page);
            $this->assertArrayHasKey('instagram_business_account', $page);
        }
        if (!empty($data['instagram_accounts'])) {
            $igAccount = $data['instagram_accounts'][0];
            $this->assertArrayHasKey('page_id', $igAccount);
            $this->assertArrayHasKey('page_name', $igAccount);
            $this->assertArrayHasKey('instagram_id', $igAccount);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramMedia(): void
    {
        // First, get Instagram Business Accounts to obtain a valid igUserId
        $accounts = $this->api->getInstagramBusinessAccounts();
        if (empty($accounts['instagram_accounts'])) {
            $this->markTestSkipped('No Instagram Business Accounts found to test getInstagramMedia');
        }

        $igUserId = $accounts['instagram_accounts'][0]['instagram_id'];
        $data = $this->api->getInstagramMedia($igUserId);

        $this->logger->debug('testGetInstagramMedia response', $data['data']);

        $this->assertIsArray($data['data']);
        if (!empty($data['data'])) {
            $media = $data['data'][0];
            $this->assertArrayHasKey('id', $media);
            $this->assertArrayHasKey('media_type', $media);
            $this->assertArrayHasKey('permalink', $media);
            $this->assertArrayHasKey('timestamp', $media);
            $this->assertArrayHasKey('caption', $media);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramMediaInsights(): void
    {
        // First, get Instagram Business Accounts to obtain a valid igUserId
        $accounts = $this->api->getInstagramBusinessAccounts();
        if (empty($accounts['instagram_accounts'])) {
            $this->markTestSkipped('No Instagram Business Accounts found to test getInstagramMediaInsights');
        }

        $igUserId = $accounts['instagram_accounts'][0]['instagram_id'];
        $mediaData = $this->api->getInstagramMedia($igUserId);
        if (empty($mediaData['data'])) {
            $this->markTestSkipped('No media found for Instagram Business Account to test getInstagramMediaInsights');
        }

        $mediaId = $mediaData['data'][0]['id'];
        $data = $this->api->getInstagramMediaInsights($mediaId, MediaProductType::from($mediaData['data'][0]['media_product_type']));

        $this->logger->debug('testGetInstagramMediaInsights response', $data['data']);

        $this->assertIsArray($data['data']);
        if (!empty($data['data'])) {
            $insight = $data['data'][0];
            $this->assertArrayHasKey('name', $insight);
            $this->assertArrayHasKey('period', $insight);
            $this->assertArrayHasKey('values', $insight);
            $this->assertIsArray($insight['values']);
            $this->assertArrayHasKey('value', $insight['values'][0]);
            $this->assertArrayHasKey('title', $insight);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramMediaInsightsPolling(): void
    {
        // First, get Instagram Business Accounts to obtain a valid igUserId
        $accounts = $this->api->getInstagramBusinessAccounts();
        if (empty($accounts['instagram_accounts'])) {
            $this->markTestSkipped('No Instagram Business Accounts found to test getInstagramMediaInsightsPolling');
        }

        $igUserId = $accounts['instagram_accounts'][0]['instagram_id'];
        $mediaData = $this->api->getInstagramMedia($igUserId);
        if (empty($mediaData['data'])) {
            $this->markTestSkipped('No media found for Instagram Business Account to test getInstagramMediaInsightsPolling');
        }

        $mediaId = $mediaData['data'][0]['id'];

        // Simulate first call (e.g., "yesterday")
        $firstData = $this->api->getInstagramMediaInsights($mediaId);

        $this->logger->debug('testGetInstagramMediaInsightsPolling first call response', $firstData['data']);

        $this->assertIsArray($firstData['data']);
        if (!empty($firstData['data'])) {
            $insight = $firstData['data'][0];
            $this->assertArrayHasKey('name', $insight);
            $this->assertArrayHasKey('period', $insight);
            $this->assertEquals('lifetime', $insight['period']);
            $this->assertArrayHasKey('values', $insight);
            $this->assertIsArray($insight['values']);
            $this->assertArrayHasKey('value', $insight['values'][0]);
            $this->assertArrayHasKey('title', $insight);
        }

        // Simulate second call (e.g., "today") after a short delay
        sleep(2); // Short delay to allow potential metric changes
        $secondData = $this->api->getInstagramMediaInsights($mediaId);

        $this->logger->debug('testGetInstagramMediaInsightsPolling second call response', $secondData['data']);

        $this->assertIsArray($secondData['data']);
        if (!empty($secondData['data'])) {
            $insight = $secondData['data'][0];
            $this->assertArrayHasKey('name', $insight);
            $this->assertArrayHasKey('period', $insight);
            $this->assertEquals('lifetime', $insight['period']);
            $this->assertArrayHasKey('values', $insight);
            $this->assertIsArray($insight['values']);
            $this->assertArrayHasKey('value', $insight['values'][0]);
            $this->assertArrayHasKey('title', $insight);
        }

        // Note: Actual daily delta calculation would require calls on separate days
        $this->logger->info('Polling test completed. For daily insights, store results daily and compute deltas.');
    }

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramAccountInsightsWithMetric(): void
    {
        // First, get Instagram Business Accounts to obtain a valid accountId
        $accounts = $this->api->getInstagramBusinessAccounts();
        if (empty($accounts['instagram_accounts'])) {
            $this->markTestSkipped('No Instagram Business Accounts found to test getInstagramAccountInsightsWithMetric');
        }

        $accountId = $accounts['instagram_accounts'][0]['instagram_id'];
        $since = date('Y-m-d', strtotime('-7 days'));
        $until = date('Y-m-d', strtotime('-1 day'));

        $data = $this->api->getInstagramAccountInsights(
            $accountId,
            $since,
            $until,
            'America/Caracas',
            Metric::REACH,
            null,
            MetricType::TIME_SERIES,
            MetricPeriod::DAY
        );

        $this->logger->debug('testGetInstagramAccountInsightsWithMetric response', $data['data']);

        $this->assertIsArray($data['data']);
        if (!empty($data['data'])) {
            $insight = $data['data'][0];
            $this->assertArrayHasKey('name', $insight);
            $this->assertEquals('reach', $insight['name']);
            $this->assertArrayHasKey('period', $insight);
            $this->assertEquals('day', $insight['period']);
            $this->assertArrayHasKey('values', $insight);
            $this->assertIsArray($insight['values']);
            $this->assertNotEmpty($insight['values']);
            $this->assertArrayHasKey('value', $insight['values'][0]);
            $this->assertArrayHasKey('end_time', $insight['values'][0]);
            $this->assertArrayHasKey('title', $insight);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramAccountInsightsWithMetricGroup(): void
    {
        // First, get Instagram Business Accounts to obtain a valid accountId
        $accounts = $this->api->getInstagramBusinessAccounts();
        if (empty($accounts['instagram_accounts'])) {
            $this->markTestSkipped('No Instagram Business Accounts found to test getInstagramAccountInsightsWithMetricGroup');
        }

        $accountId = $accounts['instagram_accounts'][0]['instagram_id'];
        $since = date('Y-m-d', strtotime('-7 days'));
        $until = date('Y-m-d', strtotime('-1 day'));

        $data = $this->api->getInstagramAccountInsights(
            $accountId,
            $since,
            $until,
            'America/Caracas',
            null,
            MetricGroup::REACH_FOLLOWERS,
            MetricType::TIME_SERIES,
            MetricPeriod::DAY
        );

        $this->logger->debug('testGetInstagramAccountInsightsWithMetricGroup response', $data['data']);

        $this->assertIsArray($data['data']);
        if (!empty($data['data'])) {
            $insight = $data['data'][0];
            $this->assertArrayHasKey('name', $insight);
            $this->assertInArray($insight['name'], ['reach', 'follower_count']);
            $this->assertArrayHasKey('period', $insight);
            $this->assertEquals('day', $insight['period']);
            $this->assertArrayHasKey('values', $insight);
            $this->assertIsArray($insight['values']);
            $this->assertNotEmpty($insight['values']);
            $this->assertArrayHasKey('value', $insight['values'][0]);
            $this->assertArrayHasKey('end_time', $insight['values'][0]);
            $this->assertArrayHasKey('title', $insight);
        }
    }

    /**
     * @throws GuzzleException
     */
    public function testGetInstagramAccountInsightsWithDemographics(): void
    {
        // First, get Instagram Business Accounts to obtain a valid accountId
        $accounts = $this->api->getInstagramBusinessAccounts();
        if (empty($accounts['instagram_accounts'])) {
            $this->markTestSkipped('No Instagram Business Accounts found to test getInstagramAccountInsightsWithDemographics');
        }

        $accountId = $accounts['instagram_accounts'][0]['instagram_id'];
        $since = date('Y-m-d', strtotime('-7 days'));
        $until = date('Y-m-d', strtotime('-1 day'));

        $data = $this->api->getInstagramAccountInsights(
            $accountId,
            $since,
            $until,
            'America/Caracas',
            Metric::FOLLOWER_DEMOGRAPHICS,
            null,
            null,
            MetricPeriod::LIFETIME,
            MetricTimeframe::THIS_MONTH,
            MetricBreakdown::AGE
        );

        $this->logger->debug('testGetInstagramAccountInsightsWithDemographics response', $data['data']);

        $this->assertIsArray($data['data']);
        if (!empty($data['data'])) {
            $insight = $data['data'][0];
            $this->assertArrayHasKey('name', $insight);
            $this->assertEquals('follower_demographics', $insight['name']);
            $this->assertArrayHasKey('period', $insight);
            $this->assertEquals('lifetime', $insight['period']);
            $this->assertArrayHasKey('values', $insight);
            $this->assertIsArray($insight['values']);
            $this->assertNotEmpty($insight['values']);
            $this->assertArrayHasKey('value', $insight['values'][0]);
            $this->assertIsArray($insight['values'][0]['value']);
            $this->assertArrayHasKey('dimension_values', $insight['values'][0]['value'][0]);
            $this->assertArrayHasKey('title', $insight);
        }
    }

    // Helper method for assertInArray
    private function assertInArray($needle, array $haystack, string $message = ''): void
    {
        $this->assertTrue(in_array($needle, $haystack), $message ?: "Failed asserting that '$needle' is in array.");
    }
}
