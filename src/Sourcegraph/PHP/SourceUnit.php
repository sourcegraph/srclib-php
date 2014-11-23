<?php

namespace Sourcegraph\PHP;

interface SourceUnit
{
    public function getType();
    public function getFiles();
    public function getPackageName($defPath);
}
