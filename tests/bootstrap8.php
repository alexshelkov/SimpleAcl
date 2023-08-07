<?php
namespace SimpleAclTest;

use PHPUnit\Framework\TestCase as PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase {
    public function setExpectedException($class, $message) {
        $this->expectException($class);
        $this->expectExceptionMessage($message);
    }

    public static function assertAttributeCount(int $expectedCount, string $haystackAttributeName, $haystackClassOrObject, string $message = ''): void {

        $reflectionProperty = new \ReflectionProperty($haystackClassOrObject, $haystackAttributeName);
        $reflectionProperty->setAccessible(true);

        static::assertSame($expectedCount, count($reflectionProperty->getValue($haystackClassOrObject)), $message);
    }

    public static function assertAttributeEquals($expected, string $actualAttributeName, $actualClassOrObject, string $message = '', float $delta = 0, int $maxDepth = 10, bool $canonicalize = false, bool $ignoreCase = false): void {
        $reflectionProperty = new \ReflectionProperty($actualClassOrObject, $actualAttributeName);
        $reflectionProperty->setAccessible(true);

        static::assertSame($expected, $reflectionProperty->getValue($actualClassOrObject), $message);
    }
}
