<?php

declare(strict_types=1);

/**
 * Minimal PSR-4 autoloader so the suite runs without Composer.
 * Maps Spark\ -> src/ and Spark\Tests\ -> tests/.
 */
spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Spark\\Tests\\' => __DIR__ . '/tests/',
        'Spark\\'        => __DIR__ . '/src/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($class, $prefix, $len) !== 0) {
            continue;
        }
        $relative = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
        if (is_file($file)) {
            require $file;
            return;
        }
    }
});
