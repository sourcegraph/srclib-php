<?php

namespace Sourcegraph\PHP;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeVisitor\NameResolver;
use Sourcegraph\PHP\NodeVisitor;
use Sourcegraph\PHP\Grapher;

class Grapher
{
    private $parser;
    private $traverser;
    private $nodeCollector;
    private $defExtractor;

    public function __construct()
    {
        $this->setUpParser();
        $this->setUpTraverser();
        $this->setUpExtractors();
    }

    protected function setUpParser()
    {
        $this->parser = new Parser(new Lexer);
    }

    protected function setUpTraverser()
    {
        $this->nodeCollector = new NodeVisitor\NodeCollector();

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NameResolver());
        $this->traverser->addVisitor($this->nodeCollector);
    }

    protected function setUpExtractors()
    {
        $this->defExtractor = new Grapher\DefExtractor();
    }

    protected function parse($code)
    {
        $stmts = $this->parser->parse($code);
        $this->traverser->traverse($stmts);

        return $this->nodeCollector->getNodes();
    }

    public function run($code)
    {
        $nodes = $this->parse($code);

        return ['defs' => $this->defExtractor->extract($nodes)];
    }
}
