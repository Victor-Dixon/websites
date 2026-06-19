<?php
declare(strict_types=1);

/**
 * Level 5 DB loader.
 *
 * Resolution order:
 * 1. Local server-only file: .level5-db.php
 * 2. Environment variables: getenv(), $_ENV, $_SERVER
 *
 * Required:
 * - LEVEL5_DB_NAME
 * - LEVEL5_DB_USER
 *
 * Optional:
 * - LEVEL5_DB_HOST defaults to localhost
 * - LEVEL5_DB_PASS defaults to empty string
 * - LEVEL5_DB_CHARSET defaults to utf8mb4
 */

function level5_secret_file_config(): array
{
    $secretFile = __DIR__ . '/.level5-db.php';

    if (!is_file($secretFile)) {
        return [];
    }

    $config = require $secretFile;

    if (!is_array($config)) {
        return [];
    }

    return $config;
}

function level5_config_value(array $fileConfig, string $key, ?string $default = null): ?string
{
    if (array_key_exists($key, $fileConfig) && $fileConfig[$key] !== '') {
        return (string) $fileConfig[$key];
    }

    $value = getenv($key);
    if ($value !== false && $value !== '') {
        return (string) $value;
    }

    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }

    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }

    return $default;
}

function level5_json_error(int $status, string $message): never
{
    http_response_code($status);

    if (!headers_sent()) {
        header('Content-Type: application/json');
    }

    echo json_encode([
        'ok' => false,
        'error' => $message,
    ]);

    exit;
}

$fileConfig = level5_secret_file_config();

$dbHost = level5_config_value($fileConfig, 'LEVEL5_DB_HOST', 'localhost');
$dbName = level5_config_value($fileConfig, 'LEVEL5_DB_NAME');
$dbUser = level5_config_value($fileConfig, 'LEVEL5_DB_USER');
$dbPass = level5_config_value($fileConfig, 'LEVEL5_DB_PASS', '');
$dbCharset = level5_config_value($fileConfig, 'LEVEL5_DB_CHARSET', 'utf8mb4');

$missing = [];

if ($dbName === null || $dbName === '') {
    $missing[] = 'LEVEL5_DB_NAME';
}

if ($dbUser === null || $dbUser === '') {
    $missing[] = 'LEVEL5_DB_USER';
}

if ($missing) {
    level5_json_error(500, 'Missing DB config: ' . implode(', ', $missing));
}

$dsn = sprintf(
    'mysql:host=%s;dbname=%s;charset=%s',
    $dbHost,
    $dbName,
    $dbCharset
);

try {
    $pdo = new PDO(
        $dsn,
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (Throwable $e) {
    level5_json_error(500, 'DB connection failed');
}
