<?php

namespace Sourcegraph\PHP\Grapher;

use Sourcegraph\PHP\Grapher;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class DocExtractor
{
    protected $defaultMIME = 'text/plain';

    public function extract($filename, Array $nodes, $test = false)
    {
        $docs = [];
        foreach ($nodes as $node) {
            if ($doc = $this->buildDoc($node, $filename, $test)) {
                $docs[] = $doc;
            }
        }

        return $docs;
    }

    private function buildDoc(Node $node, $filename, $test)
    {
        if (!$this->isIndexableNode($node)) {
            return;
        }

        if (!$this->containsComments($node)) {
            return;
        }

        return [
            'TreePath' => $this->getTreePath($node),
            'Format' => $this->defaultMIME,
            'Data' => $this->getCommentsText($node),
            'File' => $filename,
            'Start' => 0, // TODO: PHP-Parse not returns the byte position
            'End' => 0, // TODO: idem
        ];
    }

    private function isIndexableNode(Node $node)
    {
        if (
            $node instanceof Node\Stmt\Class_ ||
            $node instanceof Node\Stmt\Trait_ ||
            $node instanceof Node\Stmt\Interface_ ||
            $node instanceof Node\Stmt\Function_ ||
            $node instanceof Node\Stmt\ClassMethod ||
            $node instanceof Node\Stmt\Property ||
            $node instanceof Node\Stmt\Const_
        ) {
            return true;
        }

        return false;
    }

    private function containsComments(Node $node)
    {
        return $node->getAttribute('comments') !== null;
    }

    private function getCommentsText(Node $node)
    {
        $text = '';

        $comments = $node->getAttribute('comments');
        foreach ($comments as $comment) {
            $text .= $comment->getReformattedText();
        }

        return $text;
    }

    private function getTreePath(Node $node)
    {
        switch (true) {
            case $node instanceof Stmt\Const_:
                return $node->consts[0]->namespacedName->toString('/');
            case $node instanceof Expr\FuncCall:
                return 'TODO';
        }

        return $node->namespacedName->toString('/');
    }
}
