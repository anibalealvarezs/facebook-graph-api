<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile(__DIR__ . '/../config/config.yaml');
$appId = $config['app_id'] ?? null;
$appSecret = $config['app_secret'] ?? null;
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

$res = api_get("https://graph.facebook.com/v25.0/{$pageId}?fields=instagram_business_account,access_token&access_token={$userToken}");
$igId = $res['body']['instagram_business_account']['id'] ?? null;
$pageToken = $res['body']['access_token'] ?? null;
if (!$igId) die("IG ID not found\n");
echo "IG Account: $igId\n";

echo "Discovering Examples...\n";
$res = api_get("https://graph.facebook.com/v25.0/{$igId}/media?fields=id,media_type,media_product_type&limit=100&access_token={$userToken}");
$data = $res['body']['data'] ?? [];
$examples = [];
foreach ($data as $m) {
    $key = $m['media_product_type'] . '_' . $m['media_type'];
    if (!isset($examples[$key])) $examples[$key] = $m['id'];
}

$metrics = [
    'reach', 'total_interactions', 'likes', 'comments', 'shares', 'saved', 'views', 'replies',
    'ig_reels_video_view_total_time', 'ig_reels_avg_watch_time', 'plays', 'reposts', 'facebook_views'
];

foreach ($examples as $type => $id) {
    echo "\n--- TESTING $type ($id) ---\n";
    foreach ($metrics as $metric) {
        $url = "https://graph.facebook.com/v25.0/{$id}/insights?metric={$metric}&access_token={$userToken}";
        $r = api_get($url);
        printf("%-35s %s\n", "[$metric]", ($r['code'] === 200 ? "VALID" : "FAILED"));
    }
}

echo "\n--- TESTING PAGE ($pageId) ---\n";
$pm = ['page_impressions', 'page_post_engagements', 'page_views_total'];
foreach ($pm as $m) {
    if (!$pageToken) { echo "[$m] SKIP (No Page Token)\n"; continue; }
    $url = "https://graph.facebook.com/v25.0/{$pageId}/insights?metric={$m}&period=day&access_token={$pageToken}";
    $r = api_get($url);
    printf("%-35s %s\n", "[$m]", ($r['code'] === 200 ? "VALID" : "FAILED"));
}
