<?php

namespace Lune\Tests\Container;

use Lune\Container\Container;
use PHPUnit\Framework\TestCase;

interface MockInterface {
    public function mock(): string;
}

interface AnotherMockInterface {
    public function test(): int;
}

class MockClass implements MockInterface, AnotherMockInterface {
    public function __construct(public string $mock = "test", public int $test = 5) {
        $this->mock = $mock;
        $this->test = $test;
    }

    public function mock(): string {
        return $this->mock;
    }

    public function test(): int {
        return $this->test;
    }
}

class ContainerTest extends TestCase {
    public function testResolvesBasicObject() {
        Container::singleton(MockClass::class);
        $this->assertEquals(new MockClass(), Container::resolve(MockClass::class));
    }

    public function testResolvesInterface() {
        Container::singleton(MockInterface::class, MockClass::class);
        $this->assertEquals(new MockClass(), Container::resolve(MockInterface::class));
    }

    public function testResolvesCallbackBuiltObject() {
        Container::singleton(AnotherMockInterface::class, fn () => new MockClass("value"));
        $this->assertEquals(new MockClass("value"), Container::resolve(AnotherMockInterface::class));
    }
}
