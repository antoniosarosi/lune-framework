<?php

namespace Lune\Tests\Routing;

use Lune\Http\Controller;
use Lune\Http\HttpMethod;
use Lune\Http\Request;
use Lune\Routing\Router;
use Lune\Server\ServerData;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase {
    private function createMockRequest(string $uri, HttpMethod $method = HttpMethod::GET) {
        $mock = $this->getMockBuilder(ServerData::class)->getMock();
        $mock->method("get")->willReturnMap([
            ["REQUEST_URI", $uri],
            ["REQUEST_METHOD", $method->value],
        ]);

        $mock->method("queryParams")->willReturn([]);
        $mock->method("postData")->willReturn([]);
        $mock->method("files")->willReturn([]);

        return new Request($mock);
    }

    public function testResolveBasicRouteWithCallback() {
        $uri = "/test";
        $callback = fn () => "test";

        $router = new Router();
        $router->get($uri, $callback);
        $route = $router->resolveRoute($this->createMockRequest($uri));

        $this->assertEquals($uri, $route->uri());
        $this->assertEquals($callback, $route->action());
    }

    /**
     * @depends testResolveBasicRouteWithCallback
     */
    public function testResolveRouteWithParametersWithCallback() {
        $uri = "/test/{parameter}";
        $callback = fn () => "test";

        $router = new Router();
        $router->get($uri, $callback);
        $route = $router->resolveRoute($this->createMockRequest("/test/1"));

        $this->assertEquals($uri, $route->uri());
        $this->assertEquals($callback, $route->action());
    }

    public function testResolveBasicRouteWithControllerMethod() {
        $uri = "/test";
        $method = [Controller::class, "someMethod"];

        $router = new Router();
        $router->get($uri, $method);
        $route = $router->resolveRoute($this->createMockRequest($uri));

        $this->assertEquals($uri, $route->uri());
        $this->assertEquals($method, $route->action());
    }

    /**
     * @depends testResolveBasicRouteWithControllerMethod
     */
    public function testResolveRouteWithParametersWithControllerMethod() {
        $uri = "/test/{test}/param/{param}";
        $method = [Controller::class, "someMethod"];

        $request = $this->createMockRequest("/test/1/param/2");

        $router = new Router();
        $router->get($uri, $method);
        $route = $router->resolveRoute($request);

        $this->assertEquals($uri, $route->uri());
        $this->assertEquals($method, $route->action());
    }

    /**
     * @depends testResolveRouteWithParametersWithCallback
     * @depends testResolveRouteWithParametersWithControllerMethod
     */
    public function testResolveRoutesWithDifferentHttpMethods() {
        $routes = [
            [
                "/test",
                fn () => "test",
                "/test"
            ],
            [
                "/test/{test}",
                [Controller::class, "someMethod"],
                "/test/1"
            ],
            [
                "/test/{test}/param/{param}",
                fn () => "test",
                "/test/2/param/string"
            ],
            [
                "/users/{user}/posts/{post}/comments/{comment}",
                [Controller::class, "someMethod"],
                "/users/3/posts/4/comments/5"
            ]
        ];


        $router = new Router();

        foreach ($routes as [$routeUri, $action, $requestUri]) {
            $router->get($routeUri, $action);
        }

        foreach ($routes as [$routeUri, $action, $requestUri]) {
            $this->assertNull($router->resolveRoute($this->createMockRequest($requestUri, HttpMethod::POST)));
            $route = $router->resolveRoute($this->createMockRequest($requestUri));
            $this->assertEquals($routeUri, $route->uri());
            $this->assertEquals($action, $route->action());
        }
    }
}
