<?php
// Config/Helpers.php

// 1) Cargar ApiClient (tu clase ya existe en Config/Http)
require_once BASE_PATH . 'Config/Http/ApiClient.php';

// 2) Cargar configuraciones de APIs (tu archivo ya existe)
function api_config(string $key): ?array
{
    static $APIS = null;

    if ($APIS === null) {
        $APIS = require BASE_PATH . 'Config/Apis.php';
    }

    return $APIS[$key] ?? null;
}

// 3) Obtener endpoint según mode (testing/production)
function api_base_url(string $key): ?string
{
    $cfg = api_config($key);
    if (!$cfg) return null;

    // Si existe 'endpoints' + 'mode', usamos eso. Si no, usamos base_url.
    if (!empty($cfg['endpoints']) && !empty($cfg['mode'])) {
        return $cfg['endpoints'][$cfg['mode']] ?? ($cfg['base_url'] ?? null);
    }

    return $cfg['base_url'] ?? null;
}

// 4) Factory: construir un ApiClient listo para usarse
function api_client(string $key): ApiClient
{
    $cfg = api_config($key);
    if (!$cfg) {
        throw new Exception("API config no encontrada: {$key}");
    }

    $baseUrl = api_base_url($key);
    if (!$baseUrl) {
        throw new Exception("base_url no definido para API: {$key}");
    }

    $timeout = isset($cfg['timeout']) ? (int)$cfg['timeout'] : 25;

    return new ApiClient($baseUrl, $timeout);
}

// 5) Helper para credenciales (si van en body, como SmogsBackups)
function api_credentials(string $key): array
{
    $cfg = api_config($key);
    if (!$cfg) return [];

    return [
        'usuario'  => $cfg['usuario']  ?? '',
        'password' => $cfg['password'] ?? '',
    ];
}
