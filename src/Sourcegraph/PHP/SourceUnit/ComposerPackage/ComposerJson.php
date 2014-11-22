<?php

namespace Sourcegraph\PHP\SourceUnit\ComposerPackage;

use RuntimeException;

class ComposerJson extends JsonFile
{
    protected $file = 'composer.json';

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
