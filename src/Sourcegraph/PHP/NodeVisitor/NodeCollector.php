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
        $this->nodes[] = $node;
    }

    public function getNodes()
    {
        return $this->nodes;
    }
}
