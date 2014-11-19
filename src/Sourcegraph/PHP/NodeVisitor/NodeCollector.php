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
        $this->setNamespaceAtClasses($node);
        $this->nodes[] = $node;
    }

    private function setNamespaceAtClasses(Node $node)
    {
        if (!$node instanceof Node\Stmt\Class_) {
            return false;
        }

        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof Node\Stmt\ClassMethod) {
                $this->setNamespaceMethod($node, $stmt);
            } else if ($stmt instanceof Node\Stmt\Property) {
                $this->setNamespaceProperty($node, $stmt);
            }
        }
    }

    private function setNamespaceMethod(Node $class, Node $method)
    {
        $ns = clone $class->namespacedName;
        $ns->append($method->name);

        $method->namespacedName = $ns;
    }

    private function setNamespaceProperty(Node $class, Node $property)
    {
        $ns = clone $class->namespacedName;
        $ns->append($property->props[0]->name);

        $property->namespacedName = $ns;
    }

    public function getNodes()
    {
        return $this->nodes;
    }
}
