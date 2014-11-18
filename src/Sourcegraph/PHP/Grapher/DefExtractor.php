<?php

namespace Sourcegraph\PHP\Grapher;

use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class DefExtractor
{
    public function extract(Array $nodes)
    {
        $defs = [];
        foreach ($nodes as $node) {
            switch (true) {
                case $node instanceof Expr\Assign:
                    $defs[] = $this->extractExprAssign($node);
                    break;
                case $node instanceof Stmt\Class_:
                    $defs[] = $this->extractStmtClass($node);
                    break;
                default:
                    var_dump(get_class($node));
                    break;
            }
        }

        return $defs;
    }

    protected function extractExprAssign(Expr\Assign $node) {
        return [
            'Kind' => 'var',
            'Name' => $node->var->name
        ];
    }

    protected function extractStmtClass(Stmt\Class_ $node) {
        return [
            'Kind' => 'class',
            'Name' => (string) $node->namespacedName
        ];
    }
}
