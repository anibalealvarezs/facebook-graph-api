<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Anibalealvarezs\FacebookGraphApi\FacebookGraphApi;
use Anibalealvarezs\FacebookGraphApi\Enums\TokenSample;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaType;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaProductType;
use Anibalealvarezs\FacebookGraphApi\Enums\Metric;

/**
 * Facebook Insights Auditor Script
 * 
 * Usage: php scripts/audit_insights.php --app_id=XXX --app_secret=XXX --user_token=XXX --entity_id=XXX --type=PAGE|POST|IG_MEDIA|AD
 */

$options = getopt("", ["app_id:", "app_secret:", "user_token:", "entity_id:", "type:"]);
$appId = $options['app_id'] ?? null;
$appSecret = $options['app_secret'] ?? null;
$userToken = $options['user_token'] ?? null;
$entityId = $options['entity_id'] ?? null;
$type = strtoupper($options['type'] ?? 'PAGE');

if (!$appId || !$appSecret || !$userToken || !$entityId) {
    echo "Usage: php scripts/audit_insights.php --app_id=XXX --app_secret=XXX --user_token=XXX --entity_id=XXX --type=PAGE|POST|IG_MEDIA|AD\n";
    exit(1);
}

// Initialize API
$api = new FacebookGraphApi(
    userId: 'me',
    appId: $appId,
    appSecret: $appSecret,
    redirectUrl: 'http://localhost',
    userAccessToken: $userToken
);

echo "--- Starting Insights Audit for $type: $entityId ---\n";

$allMetrics = array_map(fn($m) => $m->value, Metric::cases());
$validMetrics = [];
$failedMetrics = [];

foreach ($allMetrics as $metric) {
    echo "Testing metric: $metric ... ";
    try {
        $res = null;
        if ($type === 'PAGE') {
            $res = $api->getFacebookPageInsights(pageId: $entityId, customMetrics: [$metric]);
        } elseif ($type === 'IG_MEDIA') {
            // Try different types if not specified
            $res = $api->getInstagramMediaInsights(mediaId: $entityId, customMetrics: [$metric]);
        }
        
        if ($res && (!empty($res['data']) || isset($res['id']))) {
            echo "VALID\n";
            $validMetrics[] = $metric;
        } else {
            echo "EMPTY\n";
        }
    } catch (\Exception $e) {
        echo "FAILED - " . $e->getMessage() . "\n";
        $failedMetrics[$metric] = $e->getMessage();
    }
}

echo "\n--- AUDIT SUMMARY FOR $type $entityId ---\n";
echo "Total Tested: " . count($allMetrics) . "\n";
echo "Total Valid: " . count($validMetrics) . "\n";
echo "Total Failed: " . count($failedMetrics) . "\n";

echo "\nVALID METRICS LIST:\n" . implode(',', $validMetrics) . "\n";

if (count($validMetrics) > 1) {
    echo "\nTesting BULK request with all valid metrics ... ";
    try {
        $res = null;
        if ($type === 'PAGE') {
            $res = $api->getFacebookPageInsights(pageId: $entityId, customMetrics: $validMetrics);
        } elseif ($type === 'IG_MEDIA') {
            $res = $api->getInstagramMediaInsights(mediaId: $entityId, customMetrics: $validMetrics);
        }
        echo "SUCCESS\n";
    } catch (\Exception $e) {
        echo "FAILED BULK - " . $e->getMessage() . "\n";
    }
}
