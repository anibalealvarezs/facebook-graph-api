<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Anibalealvarezs\FacebookGraphApi\FacebookGraphApi;
use Anibalealvarezs\FacebookGraphApi\Enums\TokenSample;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaType;
use Anibalealvarezs\FacebookGraphApi\Enums\MediaProductType;
use Anibalealvarezs\FacebookGraphApi\Enums\Metric;

use Symfony\Component\Yaml\Yaml;

/**
 * Facebook Insights Auditor Script - Advanced Version
 */

$options = getopt("", ["app_id:", "app_secret:", "user_token:", "entity_id:", "type:", "config:"]);

$configPath = $options['config'] ?? __DIR__ . '/../config/config.yaml';
$config = file_exists($configPath) ? Yaml::parseFile($configPath) : [];

$appId = $options['app_id'] ?? $config['app_id'] ?? null;
$appSecret = $options['app_secret'] ?? $config['app_secret'] ?? null;
$userToken = $options['user_token'] ?? $config['user_token'] ?? null;
$entityId = $options['entity_id'] ?? $config['page_id'] ?? null;
$type = strtoupper($options['type'] ?? 'PAGE');

if (!$appId || (!$userToken && !$appSecret) || !$entityId) {
    echo "Usage: php scripts/audit_insights.php [--config=path/to/config.yaml] [--app_id=XXX] [--app_secret=XXX] [--user_token=XXX] [--entity_id=XXX] [--type=PAGE|POST|IG_MEDIA|AD]\n";
    exit(1);
}

// Initialize API
$api = new FacebookGraphApi(
    userId: 'me',
    appId: $appId,
    appSecret: $appSecret,
    redirectUrl: 'http://localhost',
    userAccessToken: $userToken,
    sleep: 100000 // Faster for audit
);

function log_msg($msg) {
    echo $msg;
    if (php_sapi_name() === 'cli') {
        @ob_flush();
    }
}

log_msg("--- Starting Insights Audit for $type: $entityId ---\n");

// 1. Detect Real Entity Info
log_msg("Detecting entity metadata ... ");
$metadata = [];
try {
    if ($type === 'IG_MEDIA') {
        $res = $api->performRequest('GET', (string)$entityId, ['fields' => 'id,media_type,media_product_type,timestamp,caption']);
        $metadata = json_decode($res->getBody()->getContents(), true);
        log_msg("DONE\n");
        log_msg("  Detected IG Media Type: " . ($metadata['media_type'] ?? 'UNKNOWN') . "\n");
        log_msg("  Detected IG Product Type: " . ($metadata['media_product_type'] ?? 'UNKNOWN') . "\n");
    } else if ($type === 'PAGE') {
        $res = $api->performRequest('GET', (string)$entityId, ['fields' => 'id,name,category,access_token']);
        $metadata = json_decode($res->getBody()->getContents(), true);
        log_msg("DONE\n");
        log_msg("  Detected Page Name: " . ($metadata['name'] ?? 'UNKNOWN') . "\n");
        if (isset($metadata['access_token'])) {
            $api->setPageId($entityId);
            $api->setPageAccesstoken($metadata['access_token']);
            log_msg("  Using Page Access Token resolved from API.\n");
        }
    } else if ($type === 'POST') {
        $res = $api->performRequest('GET', (string)$entityId, ['fields' => 'id,message,from']);
        $metadata = json_decode($res->getBody()->getContents(), true);
        log_msg("DONE\n");
        log_msg("  Detected Post Msg: " . substr($metadata['message'] ?? 'N/A', 0, 30) . "...\n");
        $pageId = $metadata['from']['id'] ?? explode('_', (string)$entityId)[0] ?? null;
        if ($pageId) {
            log_msg("  Resolving Page Token for Page $pageId ... ");
            $pageRes = $api->performRequest('GET', (string)$pageId, ['fields' => 'access_token']);
            $pageData = json_decode($pageRes->getBody()->getContents(), true);
            if (isset($pageData['access_token'])) {
                $api->setPageId($pageId);
                $api->setPageAccesstoken($pageData['access_token']);
                log_msg("DONE\n");
            } else {
                log_msg("FAILED\n");
            }
        }
    }
} catch (Exception $e) {
    log_msg("FAILED to detect metadata: " . $e->getMessage() . "\n");
}

$allMetrics = array_map(fn($m) => $m->value, Metric::cases());
$results = [
    'valid' => [],
    'failed' => []
];

log_msg("\nTesting individual metrics ...\n");

foreach ($allMetrics as $metric) {
    // Optimization: Skip Marketing metrics for Organic content
    $metricEnum = Metric::tryFrom($metric);
    if ($metricEnum && in_array($type, ['PAGE', 'POST', 'IG_MEDIA'])) {
        if ($metricEnum->group() === \Anibalealvarezs\FacebookGraphApi\Enums\MetricGroup::MARKETING) {
            continue;
        }
    }

    log_msg(sprintf("%-40s", "Testing [$metric] ... "));
    try {
        $res = null;
        if ($type === 'PAGE') {
            $res = $api->getFacebookPageInsights(pageId: $entityId, customMetrics: [$metric], metricSet: \Anibalealvarezs\FacebookGraphApi\Enums\MetricSet::CUSTOM);
        } elseif ($type === 'IG_MEDIA') {
            // Determine type for enum
            $mTypeRaw = strtoupper($metadata['media_type'] ?? 'IMAGE');
            $pTypeRaw = strtoupper($metadata['media_product_type'] ?? 'FEED');
            
            $mType = MediaType::tryFrom($mTypeRaw) ?? MediaType::IMAGE;
            $pType = MediaProductType::tryFrom($pTypeRaw) ?? MediaProductType::FEED;
            
            // Try with pType first if it exists
            $typeToUse = ($pTypeRaw !== 'FEED') ? $pType : $mType;
            
            $res = $api->getInstagramMediaInsights(mediaId: $entityId, mediaType: $typeToUse, customMetrics: [$metric], metricSet: \Anibalealvarezs\FacebookGraphApi\Enums\MetricSet::CUSTOM);
        } elseif ($type === 'POST') {
            $res = $api->getFacebookPostInsights(postId: (string)$entityId, customMetrics: [$metric], metricSet: \Anibalealvarezs\FacebookGraphApi\Enums\MetricSet::CUSTOM);
        }
        
        if ($res && (!empty($res['data']) || isset($res['id']))) {
            log_msg("VALID\n");
            $results['valid'][] = $metric;
        } else {
            log_msg("EMPTY (Res: " . json_encode($res) . ")\n");
        }
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        log_msg("FAILED\n");
        $results['failed'][$metric] = $msg;
    }
}

log_msg("\n--- FINAL SUMMARY FOR $entityId ---\n");
log_msg("Total Metrics Tested: " . count($allMetrics) . "\n");
log_msg("Valid Metrics: " . count($results['valid']) . "\n");
log_msg("Failed Metrics: " . count(array_keys($results['failed'])) . "\n");

log_msg("\n--- VALID LIST ---\n");
log_msg(implode(',', $results['valid']) . "\n");

if (!empty($results['failed'])) {
    log_msg("\n--- FAILED DETAILS ---\n");
    foreach ($results['failed'] as $m => $err) {
        log_msg("[$m]: $err\n");
    }
}

if (count($results['valid']) > 0) {
    log_msg("\nTesting BULK request with all valid metrics ... ");
    try {
        if ($type === 'PAGE') {
            $api->getFacebookPageInsights(pageId: $entityId, customMetrics: $results['valid'], metricSet: \Anibalealvarezs\FacebookGraphApi\Enums\MetricSet::CUSTOM);
        } elseif ($type === 'IG_MEDIA') {
            $api->getInstagramMediaInsights(mediaId: $entityId, customMetrics: $results['valid'], metricSet: \Anibalealvarezs\FacebookGraphApi\Enums\MetricSet::CUSTOM);
        } elseif ($type === 'POST') {
            $api->getFacebookPostInsights(postId: (string)$entityId, customMetrics: $results['valid'], metricSet: \Anibalealvarezs\FacebookGraphApi\Enums\MetricSet::CUSTOM);
        }
        log_msg("SUCCESS\n");
    } catch (Exception $e) {
        log_msg("FAILED BULK: " . $e->getMessage() . "\n");
    }
}
