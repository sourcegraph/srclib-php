<?php

namespace Sourcegraph\PHP;

use Sourcegraph\PHP\SourceUnit\ComposerPackage;


class Scanner
{
    public function run($path)
    {
        $package = $this->buildComposerPackage($path);

        return [$package->toArray()];
    }

    protected function buildComposerPackage($path)
    {
        return new ComposerPackage($path);
    }
}
