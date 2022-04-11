<?php

namespace Lune\Storage;

/**
 * File helper.
 */
class File {
    /**
     * Instantiate new file.
     *
     * @param string $path
     * @param mixed $content
     * @param string $type
     */
    public function __construct(
        private string $path,
        private mixed $content,
        private string $type = "image"
    ) {
        $this->path = $path;
        $this->content = $content;
        $this->type = $type;
    }

    /**
     * Check if the current file is an image.
     *
     * @return boolean
     */
    public function isImage(): bool {
        return str_starts_with($this->type, "image");
    }

    /**
     * Type of the image.
     *
     * @return string|null
     */
    public function imageType(): ?string {
        return match ($this->type) {
            "image/jpeg" => "jpeg",
            "image/png" => "png",
            default => null,
        };
    }

    /**
     * Store the file.
     *
     * @return string URL.
     */
    public function store(?string $path = null): string {
        return Storage::put($path ?? $this->path, $this->content);
    }
}
