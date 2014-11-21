<?php

namespace Sourcegraph\PHP\Scanner;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveCallbackFilterIterator;
use RuntimeException;
use UnexpectedValueException;
use SplFileInfo;

class SourceUnitBuilder
{
    const COMPOSER_FILE = 'composer.json';
    const UNIT_TYPE = 'ComposerPackage';

    private $ignored = ['vendor'];
    private $extensions = ['php'];

    public function build($path)
    {
        $composer = $this->readComposerJson($path);

        return [
            'Name' => $composer['name'],
            'Type' => self::UNIT_TYPE,
            //'Repo' =>  '',
            'Globs' => [],
            'Files' => $this->getFiles($path),
            'Dependencies' => $this->getDependencies($composer),
            'Data' => ['namespaces' => $this->getNamespaces($composer)],
            'Ops' => ['depresolve' => null, 'graph' => null]
        ];
    }

    protected function readComposerJson($path)
    {
        $file = realpath($path) . DIRECTORY_SEPARATOR . self::COMPOSER_FILE;
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

        if (!$data = @json_decode($content, true)) {
            throw new RuntimeException('Unable to parse composer.json file');
        }

        return $data;
    }

    protected function getFiles($path)
    {
        $realpath = realpath($path) . DIRECTORY_SEPARATOR;
        $files = new RecursiveCallbackFilterIterator(
            new RecursiveDirectoryIterator($realpath),
            [$this, 'getFilesFilter']
        );

        $output = [];
        foreach (new RecursiveIteratorIterator($files) as $file) {
            $output[] = str_replace($realpath, '', $file->getPathname());
        }

        return $output;
    }

    public function getFilesFilter(SplFileInfo $current, $key, $iterator)
    {
        if ($this->isFileOrDirectoryIgnored($current)) {
            return false;
        }

        if ($iterator->hasChildren()) {
            return true;
        }

        if ($this->isValidFile($current)) {
            return true;
        }

        return false;
    }

    protected function isFileOrDirectoryIgnored(SplFileInfo $file)
    {
        return in_array($file->getFilename(), $this->ignored);
    }

    protected function isValidFile(SplFileInfo $file)
    {
        if (!$file->isFile()) {
            return false;
        }

        $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
        return in_array($ext, $this->extensions);
    }

    protected function getDependencies(Array $composer)
    {
        $deps = [];
        foreach (['require', 'require-dev', 'suggest'] as $key) {
            if (isset($composer[$key])) {
                $deps = array_merge($deps, $composer[$key]);
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

    protected function getNamespaces(Array $composer)
    {
        $ns = [];
        foreach (['psr-0', 'psr-4'] as $key) {
            if (isset($composer['autoload'][$key])) {
                $ns = array_merge($ns, $composer['autoload'][$key]);
            }
        }

        return array_keys($ns);
    }
}
