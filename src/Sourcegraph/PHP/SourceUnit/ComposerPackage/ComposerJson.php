<?php

namespace Sourcegraph\PHP\SourceUnit\ComposerPackage;

use RuntimeException;

class ComposerJson
{
    const FILE = 'composer.json';

    protected $path;
    protected $data;

    public function __construct($path)
    {
        $this->path = realpath($path);
        $this->read();
    }

    protected function read()
    {
        $file = $this->path . DIRECTORY_SEPARATOR . self::FILE;
        if (!file_exists($file)) {
            throw new RuntimeException(
                'Invalid Package cannot found composer.json file'
            );
        }

        if (!$content = @file_get_contents($file)) {
            throw new RuntimeException(
                'Error reading composer.json file'
            );
        }

        if (!$this->data = @json_decode($content, true)) {
            throw new RuntimeException('Unable to parse composer.json file');
        }
    }

    public function getName()
    {
        return $this->data['name'];
    }

    public function getDependencies()
    {
        $deps = [];
        foreach (['require', 'require-dev', 'suggest'] as $key) {
            if (isset($this->data[$key])) {
                $deps = array_merge($deps, $this->data[$key]);
            }
        }

        foreach ($deps as $key => $_) {
            if ($key == 'php' || $key == 'hhvm') {
                unset($deps[$key]);
                continue;
            }

            foreach (['ext-', 'lib-'] as $prefix) {
                if (strpos($key, $prefix) !== false) {
                    unset($deps[$key]);
                }
            }
        }

        return array_keys($deps);
    }

    public function getNamespaces()
    {
        $ns = [];
        foreach (['psr-0', 'psr-4'] as $key) {
            if (isset($this->data['autoload'][$key])) {
                $ns = array_merge($ns, $this->data['autoload'][$key]);
            }
        }

        return array_keys($ns);
    }
}
