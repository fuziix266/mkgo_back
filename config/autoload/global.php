<?php

use Laminas\Session\Storage\SessionArrayStorage;
use Laminas\Session\Validator\HttpUserAgent;
use Laminas\Session\Validator\RemoteAddr;

return [
    // Configuración de base de datos
    'db' => [
        'driver' => 'Pdo',
        // Uso de variables de entorno para configuración (Docker/Dokploy) con fallbacks para desarrollo local
        'dsn' => 'mysql:dbname=' . (getenv('DB_NAME') ?: 'mkgo') . ';host=' . (getenv('DB_HOST') ?: 'localhost') . ';port=' . (getenv('DB_PORT') ?: '3306') . ';charset=utf8',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'driver_options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
        ],
    ],
    
    // Configuración de sesiones
    'session_config' => [
        'cookie_lifetime' => 60 * 60 * 1, // 1 hora
        'gc_maxlifetime' => 60 * 60 * 24 * 30, // 30 días
        'name' => 'qrvehiculos_session',
        'cookie_httponly' => true,
        'cookie_secure' => false, // Cambiar a true en producción con HTTPS
        'use_cookies' => true,
        'cookie_path' => '/',
    ],
    'session_manager' => [
        'validators' => [
            RemoteAddr::class,
            HttpUserAgent::class,
        ],
    ],
    'session_storage' => [
        'type' => SessionArrayStorage::class,
    ],
];
