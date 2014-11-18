<?php

namespace Sourcegraph\PHP\NodeVisitor;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

class NodeCollector extends NodeVisitorAbstract
{

    protected $nodes;

    public function beforeTraverse(array $nodes)
    {
        $this->nodes = [];
    }

    public function enterNode(Node $node)
    {
        $this->setNamespaceToMethods($node);
        $this->nodes[] = $node;
    }

    private function setNamespaceToMethods(Node $node)
    {
        if (!$node instanceof Node\Stmt\Class_) {
            return false;
        }

        foreach ($node->stmts as $method) {
            $ns = clone $node->namespacedName;

            $ns->append($method->name);
            $method->namespacedName = $ns;
        }
    }

    public function getNodes()
    {
        return $this->nodes;
    }
}
