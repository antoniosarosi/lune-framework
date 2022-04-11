<?php

namespace Lune;

use Dotenv\Dotenv;
use Lune\Config\Config;
use Lune\Database\DB;
use Lune\Http\Controller;
use Lune\Http\Exceptions\HttpNotFoundException;
use Lune\Http\HttpMethod;
use Lune\Http\Request;
use Lune\Http\Response;
use Lune\Routing\Router;
use Lune\Server\ServerData;
use Lune\Session\Session;
use Lune\Session\Storage\SessionStorage;
use Lune\Validation\Exceptions\ValidationException;
use Lune\View\View;
use Throwable;

/**
 * App runtime.
 */
class App {
    /**
     * Root directory of the user source code.
     */
    public static string $ROOT;

    /**
     * Router instance.
     */
    public Router $router;

    /**
     * Current HTTP request.
     */
    public Request $request;

    /**
     * Controller handling current request.
     */
    public ?Controller $controller = null;

    /**
     * Template engine used to render views.
     */
    public View $view;

    /**
     * Current session.
     */
    public Session $session;

    /**
     * Create a new app instance.
     *
     * @param string $root Source code root directory.
     * @return self
     */
    public static function bootstrap(string $root): self {
        if (app(self::class)) {
            return app(self::class);
        }

        self::$ROOT = $root;

        return singleton(self::class)
            ->loadConfig()
            ->runServiceProviders("boot")
            ->setHttpHandlers()
            ->openConnections()
            ->runServiceProviders("runtime");
    }

    /**
     * Load Lune configuration.
     */
    protected function loadConfig() {
        Dotenv::createImmutable(self::$ROOT)->load();
        Config::load(self::$ROOT."/config");

        return $this;
    }

    /**
     * Register container instances.
     */
    protected function runServiceProviders(string $type) {
        foreach (config("providers.$type", []) as $provider) {
            $provider = new $provider();
            $provider->registerServices();
        }

        return $this;
    }

    /**
     * Prepare request, response,
     */
    protected function setHttpHandlers() {
        $this->request = singleton(Request::class, fn () => new Request(app(ServerData::class)));
        $this->router = singleton(Router::class);
        $this->view = app(View::class);
        $this->session = singleton(Session::class, fn () => new Session(app(SessionStorage::class)));

        return $this;
    }

    /**
     * Open database connections or other connections.
     */
    protected function openConnections() {
        DB::connect(config("database"));

        return $this;
    }

    /**
     * Application environment (dev, prod, staging...).
     *
     * @return string|bool
     */
    public function env(?string $env = null): string|bool {
        if (is_null($env)) {
            return config("app.env");
        }

        return $env == config("app.env");
    }

    /**
     * Set session variables or other parameters for the next request.
     */
    private function prepareParametersForNextRequest() {
        if ($this->request->method() == HttpMethod::GET) {
            $this->session->set('_previous', $this->request->path());
        }
    }

    /**
     * Kill the current process. If necessary, release resources here.
     *
     * @param \Lune\Http\Response $response
     */
    public function terminate(Response $response) {
        $this->prepareParametersForNextRequest();
        $response->send();
        DB::close();
        exit;
    }

    /**
     * Handle request and send response.
     */
    public function run() {
        try {
            $response = $this->router->resolve($this->request);
            $this->terminate($response);
        } catch (Throwable $e) {
            $this->handleError($e);
        }
    }

    /**
     * Respond with error.
     *
     * @param Throwable $e
     */
    private function handleError(Throwable $e) {
        match (get_class($e)) {
            ValidationException::class => $this->abort(back()->withErrors($e->errors())),
            HttpNotFoundException::class => $this->abort(view("errors/404")->setStatus(404)),
            default => $this->abort(view("errors/500", compact('e'), "error")->setStatus(500)),
        };
    }

    /**
     * Stop execution from any point.
     *
     * @param \Lune\Http\Response $response Response to send.
     */
    public function abort(Response $response) {
        $this->terminate($response);
    }
}
