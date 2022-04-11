<?php

namespace Lune\Tests\Routing;

use Lune\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase {
    public function routesWithNoParameters() {
        return [
            ["/"],
            ["/test"],
            ["/test/another"],
            ["/test/another/path"],
            ["/test/long/path/with/multiple/separators"],
        ];
    }

    public function routesWithParameters() {
        return [
            [
                "/test/{param}",
                "/test/1",
                ["param" => 1]
            ],
            [
                "/users/{user}",
                "/users/2",
                ["user" => 2]
            ],
            [
                "/test/{test}",
                "/test/string",
                ["test" => "string"]
            ],
            [
                "/test/longer/{path}",
                "/test/longer/2",
                ["path" => 2]
            ],
            [
                "/test/{param}/long/{test}/with/{multiple}/params",
                "/test/1/long/2/with/string/params",
                ["param" => 1, "test" => 2, "multiple" => "string"]
            ],
        ];
    }

    /**
     * @dataProvider routesWithNoParameters
     */
    public function testRegexMatchWithNoParameters(string $path) {
        $route = new Route($path, fn () => "test");
        $this->assertTrue($route->matches($path));
        $this->assertFalse($route->matches("$path/extra/path"));
        $this->assertFalse($route->matches("/some/$path"));
        $this->assertFalse($route->matches("/random/route"));
    }

    /**
     * @dataProvider routesWithParameters
     * @depends testRegexMatchWithNoParameters
     */
    public function testRegexMatchWithParameters(string $route, string $path) {
        $route = new Route($route, fn () => "test");
        $this->assertTrue($route->matches($path));
        $this->assertFalse($route->matches("$path/extra/path"));
        $this->assertFalse($route->matches("/some/$path"));
        $this->assertFalse($route->matches("/random/route"));
    }

    /**
     * @dataProvider routesWithParameters
     * @depends testRegexMatchWithParameters
     */
    public function testParseParameters(string $route, string $requested, array $expectedParameters) {
        $route = new Route($route, fn () => "test");
        $this->assertTrue($route->hasParameters());
        $this->assertEquals($expectedParameters, $route->parseParameters($requested));
    }
}
