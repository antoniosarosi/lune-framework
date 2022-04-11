<?php

namespace Lune\Storage\Drivers;

use Lune\App;

/**
 * Server disk storage.
 */
class DiskFileStorage implements FileStorageDriver {
    /**
     * Directory where files should be stored.
     *
     * @var string
     */
    protected string $storageDirectory;

    /**
     * URL of the application.
     *
     * @var string
     */
    protected string $appUrl;

    /**
     * URI of the public storage directory
     *
     * @var string
     */
    protected string $storageUri;

    /**
     * Instantiate disk file storage.
     *
     * @param string $storageDirectory
     */
    public function __construct(string $storageDirectory, string $appUrl, string $storageUri) {
        $this->storageDirectory = $storageDirectory;
        $this->appUrl = $appUrl;
        $this->storageUri = $storageUri;
    }

    /**
     * {@inheritdoc}
     */
    public function put(string $path, mixed $content): string {
        if (!is_dir($this->storageDirectory)) {
            mkdir($this->storageDirectory);
        }

        $directories = explode("/", $path);
        $file = array_pop($directories);
        $dir = "$this->storageDirectory/";

        if (count($directories) > 0) {
            $dir = $this->storageDirectory . "/" . implode("/", $directories);
            mkdir($dir, recursive: true);
        }

        file_put_contents("$dir/$file", $content);

        return "$this->appUrl/$this->storageUri/$path";
    }
}
