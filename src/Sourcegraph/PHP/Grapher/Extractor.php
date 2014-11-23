<?php

namespace Sourcegraph\PHP\Grapher;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use Sourcegraph\PHP\SourceUnit;
use Sourcegraph\PHP\Grapher;

interface Extractor
{
    public function extract(
        SourceUnit $unit, $filename, Array $nodes, $test = false
    );
}
