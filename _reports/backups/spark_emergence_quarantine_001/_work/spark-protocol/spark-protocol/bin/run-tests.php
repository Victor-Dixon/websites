<?php

declare(strict_types=1);

/**
 * Zero-dependency test runner.
 *
 * Discovers every tests/**\/*Test.php, instantiates each test class, runs
 * each public test* method, and reports pass/fail with assertion counts.
 * It honours expectException() the way PHPUnit does: a method that sets an
 * expected exception passes iff that exception (or a subclass) is thrown.
 *
 * Usage:  php bin/run-tests.php [--filter=Substring]
 *
 * For richer output, install PHPUnit and run `vendor/bin/phpunit` instead;
 * the same test files run under both.
 */

putenv('SPARK_BUNDLED_RUNNER=1');
$_ENV['SPARK_BUNDLED_RUNNER'] = '1';

require __DIR__ . '/../tests/bootstrap.php';

use Spark\Tests\AssertionFailed;
use Spark\Tests\TestCase;

$filter = null;
foreach ($argv as $arg) {
    if (strpos($arg, '--filter=') === 0) {
        $filter = substr($arg, strlen('--filter='));
    }
}

$root = dirname(__DIR__);
$testDirs = [$root . '/tests/Unit', $root . '/tests/Integration'];

$files = [];
foreach ($testDirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
    foreach ($it as $file) {
        if ($file->isFile() && substr($file->getFilename(), -8) === 'Test.php') {
            $files[] = $file->getPathname();
        }
    }
}
sort($files);

$totalTests = 0;
$totalPass = 0;
$failures = [];
$startAssertions = TestCase::$assertions;

foreach ($files as $file) {
    require_once $file;
}

// Collect declared test classes (those extending our TestCase).
$classes = array_filter(get_declared_classes(), static function (string $c): bool {
    return is_subclass_of($c, TestCase::class) && substr($c, -4) === 'Test';
});

foreach ($classes as $class) {
    $ref = new ReflectionClass($class);
    if ($ref->isAbstract()) {
        continue;
    }
    foreach ($ref->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
        $name = $method->getName();
        if (strpos($name, 'test') !== 0) {
            continue;
        }
        $label = $class . '::' . $name;
        if ($filter !== null && strpos($label, $filter) === false) {
            continue;
        }

        $totalTests++;
        /** @var TestCase $instance */
        $instance = $ref->newInstance();
        $instance->setUp();

        try {
            $instance->$name();
            $expected = $instance->getExpectedException();
            if ($expected !== null) {
                throw new AssertionFailed("Expected exception {$expected} was not thrown.");
            }
            $totalPass++;
            echo '.';
        } catch (AssertionFailed $e) {
            $failures[] = [$label, $e->getMessage()];
            echo 'F';
        } catch (\Throwable $e) {
            $expected = $instance->getExpectedException();
            if ($expected !== null && ($e instanceof $expected)) {
                $totalPass++;
                echo '.';
            } else {
                $failures[] = [$label, get_class($e) . ': ' . $e->getMessage()];
                echo 'E';
            }
        }
    }
}

$assertionCount = TestCase::$assertions - $startAssertions;

echo "\n\n";
if ($failures !== []) {
    echo "FAILURES:\n";
    foreach ($failures as [$label, $msg]) {
        echo "  - {$label}\n      {$msg}\n";
    }
    echo "\n";
}

printf(
    "%s — %d/%d tests passed, %d assertions.\n",
    $failures === [] ? 'OK' : 'FAILED',
    $totalPass,
    $totalTests,
    $assertionCount
);

exit($failures === [] ? 0 : 1);
