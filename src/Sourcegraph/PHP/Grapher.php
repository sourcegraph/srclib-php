<?php

namespace Sourcegraph\PHP;

use PhpParser\Parser;
use PhpParser\Lexer;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\NodeTraverser;
use PhpParser\PrettyPrinter;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeVisitor\NameResolver;
use Sourcegraph\PHP\NodeVisitor;
use Sourcegraph\PHP\Grapher;
use Sourcegraph\PHP\SourceUnit;

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

    private $unit;
    private $parser;
    private $traverser;
    private $nodeCollector;
    private $extractors;

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
        $this->extractors['Defs'] = new Grapher\DefExtractor();
        $this->extractors['Docs'] = new Grapher\DocExtractor();
        $this->extractors['Refs'] = new Grapher\RefExtractor();
    }

    private function readFile($filename)
    {
        return file_get_contents($filename);
    }

    public function run(SourceUnit $unit)
    {
        $output = [];
        $results = $this->parse($unit);
        foreach ($results as $result) {
            foreach ($this->extractors as $key => $_) {
                if (!isset($output[$key])) {
                    $output[$key] = [];
                }

                $output[$key] = array_merge($output[$key], $result[$key]);
            }
        }

        return $output;
    }

    protected function parse(SourceUnit $unit)
    {
        $result = [];
        foreach ($unit->getFiles() as $filename) {
            $result[$filename] = $this->parseFile($unit, $filename);
        }

        return $result;
    }

    protected function parseFile(SourceUnit $unit, $filename)
    {
        $nodes = $this->getNodes($unit, $filename);

        $result = [];
        foreach ($this->extractors as $key => $extractor) {
            $result[$key] = $extractor->extract($unit, $filename, $nodes);
        }

        return $result;
    }

    protected function getNodes(SourceUnit $unit, $filename)
    {
        try {
            $stmts = $this->parser->parse($this->readFile($filename));
        } catch (Error $e) {
            return [];
        }

        $this->traverser->traverse($stmts);

        return $this->nodeCollector->getNodes();
    }
}
