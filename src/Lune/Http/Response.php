<?php

namespace Lune\Http;

/**
 * HTTP Response that will be sent to the client.
 */
class Response {
    /**
     * Response content.
     *
     * @var ?string
     */
    protected ?string $content = null;

    /**
     * HTTP Response status code.
     *
     * @var int
     */
    protected int $status = 200;

    /**
     * HTTP headers.
     */
    protected array $headers = [];

    /**
     * Set the response HTTP status code.
     *
     * @param int $status
     * @return $this
     */
    public function setStatus(int $status): self {
        $this->status = $status;

        return $this;
    }

    /**
     * Set HTTP header, or override existing one.
     *
     * @param string $header
     * @param string $value
     * @return $this
     */
    public function setHeader(string $header, string $value): self {
        $this->headers[$header] = $value;

        return $this;
    }

    /**
     * Remove previously set header.
     * @param string header
     */
    public function removeHeader(string $header) {
        if (array_key_exists($header, $this->headers)) {
            unset($this->headers[$header]);
        }
    }

    /**
     * Set HTTP content type ("text/html", "application/json", ...).
     *
     * @param string $contentType
     * @return $this
     */
    public function setContentType(string $contentType): self {
        return $this->setHeader("Content-Type", $contentType);
    }

    /**
     * Set response content.
     *
     * @param string $content
     * @return $this
     */
    public function setContent(string $content): self {
        $this->content = $content;

        return $this;
    }

    /**
     * Flash errors into session.
     *
     * @param array $errors
     * @param int $status
     * @return $this
     */
    public function withErrors(array $errors, int $status = 400): self {
        $this->setStatus($status);
        session()
            ->flash('errors', $errors)
            ->flash('old', request()->data());

        return $this;
    }

    /**
     * Check if the response has any content.
     */
    public function hasContent(): bool {
        return $this->content != null;
    }

    /**
     * Get the content of this response.
     *
     * @return ?string
     */
    public function content(): ?string {
        return $this->content;
    }

    /**
     * Send the response headers.
     */
    public function sendHeaders() {
        http_response_code($this->status);
        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }
    }

    /**
     * Send response content.
     */
    public function sendContent() {
        echo $this->content();
    }

    /**
     * Check HTTP headers before sending.
     */
    public function prepare(): self {
        if ($this->content == null) {
            $this->removeHeader("Content-Type");
            $this->removeHeader("Content-Length");
        }

        return $this;
    }

    /**
     * Write HTTP headers and response content.
     */
    public function send() {
        $this->prepare();
        $this->sendHeaders();
        $this->sendContent();
    }

    /**
     * Create a new response preconfigured to return a rendered template.
     *
     * @param string $view
     * @param array $params
     * @param ?string $layout
     * @return self
     */
    public static function view(
        string $view,
        array $params = [],
        ?string $layout = null
    ): self {
        $layout ??= app()->controller?->layout;
        $content = app()->view->render($view, $params, $layout);

        return (new self())
            ->setContentType("text/html")
            ->setContent($content);
    }

    /**
     * Create a new respone preconfigured to return JSON content.
     *
     * @param array $json
     * @return self
     */
    public static function json(array $json): self {
        return (new self())
            ->setContentType("application/json")
            ->setContent(json_encode($json));
    }

    /**
     * Create a new respone preconfigured to return plain text content.
     *
     * @param string $text
     * @return self
     */
    public static function text(string $text): self {
        return (new self())
            ->setContentType("text/plain")
            ->setContent($text);
    }

    /**
     * Create a new redirect response.
     *
     * @param string $route
     * @return self
     */
    public static function redirect(string $route): self {
        return (new self())
            ->setStatus(302)
            ->setHeader("Location", $route);
    }
}
