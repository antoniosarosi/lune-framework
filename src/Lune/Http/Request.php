<?php

namespace Lune\Http;

use Lune\Server\ServerData;
use Lune\Storage\File;
use Lune\Validation\Validator;

/**
 * HTTP Request sent by the client.
 */
class Request {
    /**
     * Path part of the request URI
     *
     * @var string
     */
    private string $path;

    /**
     * HTTP Method (GET, POST, ...)
     *
     * @var HTTPMethod
     */
    private HttpMethod $method;

    /**
     * Request data sent with POST or PUT methods
     *
     * @var array
     */
    private ?array $data;

    /**
     * Query parameters
     *
     * @var array
     */
    private ?array $query;


    /**
     * Uploaded files.
     *
     * @var array<string, \Lune\Storage\File>
     */
    private ?array $files;

    /**
     * Create a new request instance.
     *
     * @return self
     */
    public function __construct(ServerData $server) {
        $this->path = parse_url($server->get("REQUEST_URI"), PHP_URL_PATH);
        $this->method = HttpMethod::from($server->get("REQUEST_METHOD"));
        $this->data = $this->sanitize($server->postData(), INPUT_POST);
        $this->query = $this->sanitize($server->queryParams(), INPUT_GET);
        ;
        $this->files = $this->createFiles($server->files());

        return $this;
    }

    /**
     * Remove special chars from given data.
     *
     * @param array $data
     * @param int $type One of `INPUT_GET`, `INPUT_POST`
     * @return array
     */
    protected function sanitize(array $data, int $type): array {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = filter_input($type, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $sanitized;
    }

    /**
     * Convert received files into File objects.
     *
     * @param array $from
     * @return array
     */
    protected function createFiles(array $from): array {
        $files = [];
        foreach ($from as $key => $file) {
            if (!empty($file["tmp_name"])) {
                $basename = basename($file["tmp_name"]);
                $files[$key] = new File(
                    $basename,
                    file_get_contents($file["tmp_name"]),
                    $file["type"]
                );
            }
        }

        return $files;
    }

    /**
     * Get the path portion of the URI (example: app.com/route/1 returns /route/1)
     *
     * @return string
     */
    public function path(): string {
        return $this->path;
    }

    /**
     * Get the request HTTP method.
     *
     * @return HttpMethod
     */
    public function method(): HttpMethod {
        return $this->method;
    }

    /**
     * Get all the query parameters or specific ones.
     *
     * @param string $key
     * @return string|array|null
     */
    public function query(?string $key): string|array|null {
        return $key ? $this->query[$key] ?? null : $this->query;
    }

    /**
     * Get specific key from request data.
     *
     * @return ?string
     */
    public function get(string $key): ?string {
        return $this->data[$key] ?? null;
    }

    /**
     * Get all the request data.
     *
     * @return array
     */
    public function data(): array {
        return $this->data;
    }

    /**
     * Get file from request.
     *
     * @param string $name
     * @return File|null
     */
    public function file(string $name): ?File {
        return $this->files[$name] ?? null;
    }

    /**
     * Get validated data from the request or return back with errors if not valid.
     *
     * @param array $rules
     * @param array $messages
     * @return array
     */
    public function validate(array $rules, array $messages = []) {
        $validator = new Validator($this->data);

        return $validator->validate($rules, $messages);
    }
}
