<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Yaml\Yaml;

$configFile = getenv('CONFIG_FILE') ?: __DIR__ . '/../config/config.yaml';

if (file_exists($configFile)) {
    $GLOBALS['app_config'] = Yaml::parseFile($configFile);
} else {
    $GLOBALS['app_config'] = [];
}

function app_config(string $key = null, $default = null)
{
    $config = $GLOBALS['app_config'] ?? [];
    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}
