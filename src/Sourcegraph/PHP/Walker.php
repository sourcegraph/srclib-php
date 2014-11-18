<?php

namespace Sourcegraph\PHP;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeVisitor\NameResolver;
use Iterator;

class Walker
{
    private $nodes;

    public function __construct(Array $nodes)
    {
        print_r($nodes);
        $this->nodes = $nodes;
    }

    public function walk()
    {
        return $this->iterateNodes($this->nodes);
    }

    protected function iterateNodes(Array $nodes)
    {
        $result = [];
        foreach ($nodes as $node) {
            $result[] = $node;
            $result = array_merge($this->processSubnodes($node), $result);
        }

        return $result;
    }

    private function processSubnodes(Node $node)
    {
        $result = [];
        foreach ($node->getIterator() as $nodes) {
            if ($nodes instanceof Node) {
                $nodes = [$nodes];
            } else {
                var_dump($nodes);
            }

            $result = array_merge(
                $this->iterateNodes($nodes),
                $result
            );
        }

        return $result;
    }
}
