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

$res = api_get("https://graph.facebook.com/v25.0/{$pageId}?fields=access_token&access_token={$userToken}");
$pageToken = $res['body']['access_token'] ?? null;
if (!$pageToken) die("Page Access Token not found\n");

echo "--- AUDITING PAGE ($pageId) ---\n";
$pageMetrics = [
    'page_impressions', 'page_impressions_unique', 'page_post_engagements', 
    'page_views_total', 'page_fans', 'page_fan_adds', 'page_follows',
    'page_actions_post_reactions_total', 'page_video_views'
];

foreach ($pageMetrics as $m) {
    $url = "https://graph.facebook.com/v25.0/{$pageId}/insights?metric={$m}&period=day&access_token={$pageToken}";
    $r = api_get($url);
    printf("%-35s %s\n", "[$m]", ($r['code'] === 200 ? "VALID" : "FAILED (" . ($r['body']['error']['message'] ?? 'Unknown error') . ")"));
}

echo "\n--- DISCOVERING POSTS ---\n";
$res = api_get("https://graph.facebook.com/v25.0/{$pageId}/posts?limit=5&access_token={$pageToken}");
$posts = $res['body']['data'] ?? [];
if (empty($posts)) die("No posts found to test.\n");

$postId = $posts[0]['id'];
echo "Testing Post: $postId\n";

$postMetrics = [
    'post_impressions', 'post_impressions_unique', 'post_engagements', 
    'post_clicks', 'post_reactions_by_type_total', 'post_media_view',
    'post_video_views', 'post_video_avg_time_watched'
];

foreach ($postMetrics as $m) {
    $url = "https://graph.facebook.com/v25.0/{$postId}/insights?metric={$m}&access_token={$pageToken}";
    $r = api_get($url);
    printf("%-35s %s\n", "[$m]", ($r['code'] === 200 ? "VALID" : "FAILED (" . ($r['body']['error']['message'] ?? 'Unknown error') . ")"));
}
