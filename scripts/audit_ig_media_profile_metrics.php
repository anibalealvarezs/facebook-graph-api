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

$res = api_get("https://graph.facebook.com/v25.0/{$igId}/media?limit=1&access_token={$userToken}");
$mediaId = $res['body']['data'][0]['id'] ?? null;
if (!$mediaId) die("No media found\n");

echo "--- AUDITING IG MEDIA ($mediaId) ---\n";
$metrics = [
    'profile_visits', 'profile_activity', 'profile_views', 'saved', 'saves'
];

foreach ($metrics as $m) {
    $url = "https://graph.facebook.com/v25.0/{$mediaId}/insights?metric={$m}&access_token={$userToken}";
    $r = api_get($url);
    printf("%-35s %s\n", "[$m]", ($r['code'] === 200 ? "VALID" : "FAILED (" . ($r['body']['error']['message'] ?? 'Unknown error') . ")"));
}
