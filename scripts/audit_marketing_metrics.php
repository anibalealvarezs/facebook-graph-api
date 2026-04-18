<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile(__DIR__ . '/../config/config.yaml');
$appId = $config['app_id'] ?? null;
$userToken = $config['user_token'] ?? null;

function api_get($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return ['body' => json_decode($response, true), 'code' => $info['http_code']];
}

echo "--- DISCOVERING AD ACCOUNTS ---\n";
$res = api_get("https://graph.facebook.com/v25.0/me/adaccounts?fields=id,name&access_token={$userToken}");
$accounts = $res['body']['data'] ?? [];
if (empty($accounts)) die("No Ad Accounts found.\n");

$adAccountId = $accounts[0]['id'];
echo "Testing Ad Account: $adAccountId ({$accounts[0]['name']})\n";

$metrics = [
    'spend', 'impressions', 'reach', 'clicks', 'ctr', 'cpc', 'frequency', 
    'cost_per_result', 'results', 'purchase_roas', 'actions', 'cpm'
];

$fields = implode(',', $metrics);
$url = "https://graph.facebook.com/v25.0/{$adAccountId}/insights?fields={$fields}&date_preset=last_30d&access_token={$userToken}";
$r = api_get($url);

if ($r['code'] === 200) {
    echo "SUCCESS: Group request VALID.\n";
    print_r($r['body']['data'][0] ?? 'No data for last 30d');
} else {
    echo "FAILED: Group request error: " . ($r['body']['error']['message'] ?? 'Unknown error') . "\n";
    echo "Testing individually...\n";
    foreach ($metrics as $m) {
        $url = "https://graph.facebook.com/v25.0/{$adAccountId}/insights?fields={$m}&date_preset=last_30d&access_token={$userToken}";
        $r_ind = api_get($url);
        printf("%-35s %s\n", "[$m]", ($r_ind['code'] === 200 ? "VALID" : "FAILED (" . ($r_ind['body']['error']['message'] ?? 'Unknown error') . ")"));
    }
}
