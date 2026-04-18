<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile(__DIR__ . '/../config/config.yaml');
$appId = $config['app_id'] ?? null;
$userToken = $config['user_token'] ?? null;
$pageId = $config['page_id'] ?? null;

function api_get($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['body' => json_decode($response, true), 'code' => $info['http_code']];
}

$res = api_get("https://graph.facebook.com/v25.0/{$pageId}?fields=instagram_business_account&access_token={$userToken}");
$igId = $res['body']['instagram_business_account']['id'] ?? null;
if (!$igId) die("IG ID not found\n");

echo "--- AUDITING IG ACCOUNT ($igId) ---\n";
$metrics = [
    'reach', 'views', 'follower_count', 'accounts_engaged', 'total_interactions',
    'likes', 'comments', 'shares', 'saved', 'replies', 'profile_visits',
    'profile_links_taps', 'website_clicks', 'content_views', 'follows_and_unfollows'
];

$since = date('Y-m-d', strtotime('-2 days'));
$until = date('Y-m-d', strtotime('-1 days'));

foreach ($metrics as $m) {
    // Some metrics are total_value, some time_series
    $url = "https://graph.facebook.com/v25.0/{$igId}/insights?metric={$m}&period=day&since={$since}&until={$until}&metric_type=total_value&access_token={$userToken}";
    $r = api_get($url);
    if ($r['code'] !== 200) {
        // Try time_series
        $url = "https://graph.facebook.com/v25.0/{$igId}/insights?metric={$m}&period=day&since={$since}&until={$until}&metric_type=time_series&access_token={$userToken}";
        $r = api_get($url);
    }
    printf("%-35s %s\n", "[$m]", ($r['code'] === 200 ? "VALID" : "FAILED (" . ($r['body']['error']['message'] ?? 'Unknown error') . ")"));
}
