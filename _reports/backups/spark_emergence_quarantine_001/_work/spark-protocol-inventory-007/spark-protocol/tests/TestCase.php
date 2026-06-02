<?php

declare(strict_types=1);

namespace Spark\Tests;

/**
 * Tiny zero-dependency test base. Mirrors a subset of the PHPUnit API
 * (assertSame, assertTrue, expectException, etc.) so the same test files
 * run under real PHPUnit *or* the bundled bin/run-tests.php runner.
 *
 * If PHPUnit's TestCase is present, this file is not loaded (see
 * tests/bootstrap.php). When PHPUnit is absent, the runner uses this.
 */
abstract class TestCase
{
    /** @var int */
    public static $assertions = 0;

    /** @var string|null Expected exception class for the current test. */
    private $expectedException;

    public function setUp(): void
    {
    }

    protected function expectException(string $class): void
    {
        $this->expectedException = $class;
    }

    public function getExpectedException(): ?string
    {
        return $this->expectedException;
    }

    protected function fail(string $message): void
    {
        throw new AssertionFailed($message);
    }

    protected function assertTrue($cond, string $msg = ''): void
    {
        self::$assertions++;
        if ($cond !== true) {
            throw new AssertionFailed($msg !== '' ? $msg : 'Failed asserting that value is true.');
        }
    }

    protected function assertFalse($cond, string $msg = ''): void
    {
        self::$assertions++;
        if ($cond !== false) {
            throw new AssertionFailed($msg !== '' ? $msg : 'Failed asserting that value is false.');
        }
    }

    protected function assertSame($expected, $actual, string $msg = ''): void
    {
        self::$assertions++;
        if ($expected !== $actual) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') .
                'Failed asserting two values are identical. Expected ' .
                var_export($expected, true) . ', got ' . var_export($actual, true) . '.'
            );
        }
    }

    protected function assertEquals($expected, $actual, string $msg = ''): void
    {
        self::$assertions++;
        if ($expected != $actual) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') .
                'Failed asserting equality. Expected ' .
                var_export($expected, true) . ', got ' . var_export($actual, true) . '.'
            );
        }
    }

    protected function assertEqualsWithDelta(float $expected, float $actual, float $delta, string $msg = ''): void
    {
        self::$assertions++;
        if (abs($expected - $actual) > $delta) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') .
                "Failed asserting {$actual} is within {$delta} of {$expected}."
            );
        }
    }

    protected function assertGreaterThanOrEqual($floor, $actual, string $msg = ''): void
    {
        self::$assertions++;
        if (!($actual >= $floor)) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') . "Failed asserting {$actual} >= {$floor}."
            );
        }
    }

    protected function assertLessThanOrEqual($ceil, $actual, string $msg = ''): void
    {
        self::$assertions++;
        if (!($actual <= $ceil)) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') . "Failed asserting {$actual} <= {$ceil}."
            );
        }
    }

    protected function assertContains($needle, array $haystack, string $msg = ''): void
    {
        self::$assertions++;
        if (!in_array($needle, $haystack, true)) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') .
                'Failed asserting array contains ' . var_export($needle, true) . '.'
            );
        }
    }

    protected function assertCount(int $expected, array $actual, string $msg = ''): void
    {
        self::$assertions++;
        if (count($actual) !== $expected) {
            throw new AssertionFailed(
                ($msg !== '' ? $msg . ' — ' : '') .
                "Failed asserting count {$expected}, got " . count($actual) . '.'
            );
        }
    }
}
