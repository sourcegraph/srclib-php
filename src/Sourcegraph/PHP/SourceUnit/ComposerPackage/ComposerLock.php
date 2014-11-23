<?php

namespace Sourcegraph\PHP\SourceUnit\ComposerPackage;

use RuntimeException;

class ComposerLock extends JsonFile
{
    protected $file = 'composer.lock';
    protected $repositories = [];
    protected $namespaces = [];

    public function __construct($path)
    {
        parent::__construct($path);
        $this->parsePackages();
    }

    protected function parsePackages()
    {
        if (!isset($this->data['packages'])) {
            $this->data['packages'] = [];
        }

        foreach ($this->data['packages'] as $package) {
            $this->parsePackage($package);
        }

        if (!isset($this->data['packages-dev'])) {
            $this->data['packages-dev'] = [];
        }

        foreach ($this->data['packages-dev'] as $package) {
            $this->parsePackage($package);
        }
    }

    protected function parsePackage(Array $package)
    {
        $this->parsePackageSource($package['name'], $package);
        $this->parsePackageNamespaces($package['name'], $package);
    }

    protected function parsePackageSource($name, Array $package)
    {
        $this->repositories[$name] = $package['source'];
    }

    protected function parsePackageNamespaces($name, Array $package)
    {
        $ns = [];
        foreach (['psr-0', 'psr-4'] as $key) {
            if (isset($package['autoload'][$key])) {
                $ns = array_merge($ns, $package['autoload'][$key]);
            }
        }

        foreach ($ns as $namespace => $path) {
            $this->namespaces[$namespace] = ['name' => $name, 'path' => $path];
        }

        krsort($this->namespaces);
    }

    public function getPackageName($namespace)
    {
        foreach ($this->namespaces as $ns => $package) {
            if (stripos($namespace, $ns) !== false) {
                return $package['name'];
            }
        }

        return null;
    }

    public function getRepository($packageName)
    {
        if (!isset($this->repositories[$packageName]['url'])) {
            return null;
        }

        return strtolower($this->repositories[$packageName]['url']);
    }

    public function getCommit($packageName)
    {
        if (!isset($this->repositories[$packageName]['reference'])) {
            return null;
        }

        return $this->repositories[$packageName]['reference'];
    }
}
