<?php

namespace Sourcegraph\PHP\SourceUnit;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveCallbackFilterIterator;
use RuntimeException;
use UnexpectedValueException;
use SplFileInfo;
use Sourcegraph\PHP\SourceUnit\ComposerPackage\ComposerJson;

class ComposerPackage
{
    const TYPE = 'ComposerPackage';

    private $ignored = ['vendor'];
    private $extensions = ['php'];

    protected $path;
    protected $json;

    public function __construct($path)
    {
        $this->path = $path;
        $this->json = new ComposerJson($path);
    }

    public function getName()
    {
        return $this->json->getName();
    }

    public function getDependencies()
    {
        return $this->json->getDependencies();
    }

    public function getNamespaces()
    {
        return $this->json->getNamespaces();
    }

    public function getFiles()
    {
        $realpath = realpath($this->path) . DIRECTORY_SEPARATOR;
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

    public function toArray()
    {
        return [
            'Name' => $this->getName(),
            'Type' => self::TYPE,
            'Globs' => [],
            'Files' => $this->getFiles(),
            'Dependencies' => $this->getDependencies(),
            'Data' => ['namespaces' => $this->getNamespaces()],
            'Ops' => ['depresolve' => null, 'graph' => null]
        ];
    }
}
