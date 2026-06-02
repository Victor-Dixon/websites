<?php

declare(strict_types=1);

/**
 * Test bootstrap.
 *
 * Loads the autoloader, then decides which TestCase base the suite uses:
 *   - If PHPUnit is installed (vendor/ present), tests extend PHPUnit's
 *     TestCase via the alias below and run under `vendor/bin/phpunit`.
 *   - If PHPUnit is absent, the bundled bin/run-tests.php sets
 *     SPARK_BUNDLED_RUNNER=1 first, so we keep the zero-dependency
 *     Spark\Tests\TestCase that the runner understands.
 *
 * Either way the test files themselves are identical: they `use
 * Spark\Tests\TestCase`. Under PHPUnit that name is aliased to the real
 * framework class; under the bundled runner it is our own.
 */

require __DIR__ . '/../autoload.php';

$usingBundled = getenv('SPARK_BUNDLED_RUNNER') === '1';

if (!$usingBundled && class_exists(\PHPUnit\Framework\TestCase::class)) {
    // Alias our namespace name to PHPUnit's TestCase so test files that
    // `use Spark\Tests\TestCase` transparently get the real framework.
    if (!class_exists('Spark\\Tests\\TestCase', false)) {
        class_alias(\PHPUnit\Framework\TestCase::class, 'Spark\\Tests\\TestCase');
    }
}
// Otherwise the autoloader resolves Spark\Tests\TestCase to our bundled one.
