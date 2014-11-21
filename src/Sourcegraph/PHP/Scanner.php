<?php

namespace Sourcegraph\PHP;

use Sourcegraph\PHP\Scanner\SourceUnitBuilder;

class Scanner
{
    protected $builder;

    public function __construct()
    {
        $this->setUpSourceUnitBuilder();
    }

    protected function setUpSourceUnitBuilder()
    {
        $this->builder = new SourceUnitBuilder();
    }

    public function run($path)
    {
        return [$this->builder->build($path)];
    }
}
