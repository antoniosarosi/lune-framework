<?php

namespace Lune\Tests\Container;

use Lune\Container\Container;
use Lune\Container\DependencyInjection;
use Lune\Crypto\Hasher;
use Lune\Database\Model;
use Lune\Http\Request;
use Lune\Server\ServerData;
use Lune\Storage\Drivers\FileStorageDriver;
use PHPUnit\Framework\TestCase;

class MockModel extends Model {
    public static function find(int $id): ?static {
        $model = new self();
        $model->id = 1;

        return $model;
    }
}

class DependencyInjectionTest extends TestCase {
    public function testResolvesCallbackParametersFromContainer() {
        $callback = fn (Request $request, Hasher $hasher) => "test";

        $request = $this->getMockBuilder(Request::class)->disableOriginalConstructor();
        $hasher = $this->getMockBuilder(Hasher::class)->disableOriginalConstructor();

        Container::singleton(Request::class, fn () => $request);
        Container::singleton(Hasher::class, fn () => $hasher);

        $this->assertEquals([$request, $hasher], DependencyInjection::resolveParameters($callback));
    }

    public function testResolvesCallbackParametersFromRouteParameters() {
        $callback = fn (int $id, string $param) => "test";

        $routeParams = ["id" => 1, "param" => "test"];

        $this->assertEquals(
            array_values($routeParams),
            DependencyInjection::resolveParameters($callback, $routeParams)
        );
    }

    /**
     * @depends testResolvesCallbackParametersFromContainer
     * @depends testResolvesCallbackParametersFromRouteParameters
     */
    public function testResolvesCallbackParametersFromContainerAndRouteParameters() {
        $callback = fn (int $id, string $param, FileStorageDriver $storage, ServerData $server) => "test";

        $storage = $this->getMockBuilder(FileStorageDriver::class);
        $server = $this->getMockBuilder(ServerData::class);

        Container::singleton(FileStorageDriver::class, fn () => $storage);
        Container::singleton(ServerData::class, fn () => $server);

        $routeParams = ["id" => 1, "param" => "test"];

        $this->assertEquals(
            [1, "test", $storage, $server],
            DependencyInjection::resolveParameters($callback, $routeParams)
        );
    }

    public function testResolvesModelFromDatabase() {
        $callback = fn (MockModel $model) => "test";

        $model = MockModel::find(1);

        $routeParams = ["mock_model" => 1];

        $this->assertEquals(
            [$model],
            DependencyInjection::resolveParameters($callback, $routeParams)
        );
    }
}
