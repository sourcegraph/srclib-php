<?php

namespace Sourcegraph\PHP\SourceUnit\ComposerPackage;

use RuntimeException;

abstract class JsonFile
{
    protected $file;
    protected $path;
    protected $data;

    public function __construct($path)
    {
        $this->path = realpath($path);
        $this->read();
    }

    protected function read()
    {
        $file = $this->path . DIRECTORY_SEPARATOR . $this->file;
        if (!file_exists($file)) {
            throw new RuntimeException(
                'Invalid Package cannot found ' . $this->file . ' file'
            );
        }

        if (!$content = @file_get_contents($file)) {
            throw new RuntimeException(
                'Error reading ' . $this->file . ' file'
            );
        }

        if (!$this->data = @json_decode($content, true)) {
            throw new RuntimeException('Unable to parse ' . $this->file . ' file');
        }
    }
}
