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
    const KIND_CLASS = 'class';
    const KIND_ASSIGN = 'assign';
    const KIND_FUNCTION = 'function';
    const KIND_METHOD = 'method';
    const KIND_CONSTANT = 'constant';
    const KIND_PROPERTY = 'property';
    const KIND_TRAIT = 'trait';
    const KIND_INTERFACE = 'interface';

    private $projectPath;
    private $parser;
    private $traverser;
    private $nodeCollector;
    private $defExtractor;

    public function __construct($projectPath)
    {
        $this->projectPath = realpath($projectPath);

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
        $this->docExtractor = new Grapher\DocExtractor();
    }

    protected function parse($filename)
    {
        $stmts = $this->parser->parse($this->readFile($filename));
        $this->traverser->traverse($stmts);

        return $this->nodeCollector->getNodes();
    }

    private function readFile($filename)
    {
        return file_get_contents($filename);
    }

    public function run($filename)
    {
        $nodes = $this->parse($filename);
        $filename = $this->getRelativeFilename($filename);

        return [
            'defs' => $this->defExtractor->extract($filename, $nodes),
            'docs' => $this->docExtractor->extract($filename, $nodes),
        ];
    }

    private function getRelativeFilename($filename)
    {
        $filename = realpath($filename);

        return str_replace($this->projectPath, '', $filename);
    }
}
