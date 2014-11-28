<?php

namespace Sourcegraph\PHP\Grapher;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt;
use PhpParser\Node\Expr;
use Sourcegraph\PHP\SourceUnit;
use Sourcegraph\PHP\Grapher;

class RefExtractor implements Extractor
{
    public function extract(SourceUnit $unit, $filename, Array $nodes, $test = false)
    {
        $refs = [];
        foreach ($nodes as $node) {
            switch (true) {
                case $node instanceof Stmt\Class_:
                    $r = $this->buildRefFromClass($node, $unit, $filename, $test);
                    break;
                case $node instanceof Stmt\TraitUse:
                    $r = $this->buildRefFromTraitUse($node, $unit, $filename, $test);
                    break;
                default:
                    $r = [];
                    if ($ref = $this->buildRef($node, $unit, $filename, $test)) {
                        $r[] = $ref;
                    }
                    break;
            }

            $refs = array_merge($refs, $r);
        }

        return $refs;
    }

    protected function buildRefFromClass(Stmt\Class_ $node, SourceUnit $unit, $filename, $test)
    {
        $result = [];
        $names = $node->implements;
        if ($node->extends) {
            $names[] = $node->extends;
        }

        foreach ($names as $name) {
            $ref = $this->extractNameFullyQualified($name, $filename, $test);
            $this->applyGlobal($ref, $name, $unit, $filename, $test);
            $result[] = $ref;
        }

        return $result;
    }


    protected function buildRefFromTraitUse(Stmt\TraitUse $node, SourceUnit $unit, $filename, $test)
    {
        $result = [];
        foreach ($node->traits as $name) {
            $ref = $this->extractNameFullyQualified($name);
            $this->applyGlobal($ref, $name, $unit, $filename, $test);
            $result[] = $ref;
        }

        return $result;
    }

    protected function extractNameFullyQualified(Name\FullyQualified $node)
    {
        return [
            'DefPath' => $node->toString('/')
        ];
    }

    protected function buildRef(Node $node, SourceUnit $unit, $filename, $test)
    {
        switch (true) {
            case $node instanceof Param:
                $ref = $this->extractParam($node);
                break;
            case $node instanceof Stmt\Use_:
                $ref = $this->extractStmtUse($node);
                break;
            case $node instanceof Expr\ConstFetch:
                $ref = $this->extractExprConstFetch($node);
                break;
            case $node instanceof Expr\ClassConstFetch:
                $ref = $this->extractExprClassConstFetch($node);
                break;
            case $node instanceof Expr\New_:
                $ref = $this->extractExprNew($node);
                break;
            case $node instanceof Expr\Assign:
                $ref = $this->extractExprAssign($node);
                break;
            default:
                return null;
        }

        if ($ref) {
            $this->applyGlobal($ref, $node, $unit, $filename, $test);
        }

        return $ref;
    }

    protected function extractParam(Param $node)
    {
        if ($node->type && $node->type != 'array') {
            return $this->extractParamFromType($node);
        }

        if ($node->default && $node->default instanceof Expr\ClassConstFetch) {
            return $this->extractExprClassConstFetch($node->default);
        }

        if ($node->default && $node->default instanceof Expr\ConstFetch) {
            return $this->extractExprConstFetch($node->default);
        }
    }

    protected function extractParamFromType(Param $node)
    {
        return [
            'DefPath' => $node->type->toString('/')
        ];
    }

    protected function extractStmtUse(Stmt\Use_ $node)
    {
        return [
            'DefPath' => $node->uses[0]->name->toString('/')
        ];
    }

    protected function extractExprAssign(Expr\Assign $node)
    {
        if ($node->expr && $node->expr instanceof Expr\New_) {
            return $this->extractExprNew($node->expr);
        }

        if ($node->expr && $node->expr instanceof Expr\ConstFetch) {
            return $this->extractExprConstFetch($node->expr);
        }

        if ($node->expr && $node->expr instanceof Expr\ClassConstFetch) {
            return $this->extractExprClassConstFetch($node->expr);
        }
    }


    protected function extractExprClassConstFetch(Expr\ClassConstFetch $node)
    {
        $name = clone $node->class;
        $name->append($node->name);

        return [
            'DefPath' => $name->toString('/')
        ];
    }

    protected function extractExprConstFetch(Expr\ConstFetch $node)
    {
        return [
            'DefPath' => $node->name->toString('/')
        ];
    }

    protected function extractExprNew(Expr\New_ $node)
    {
        return [
            'DefPath' => $node->class->toString('/')
        ];
    }

    protected function applyGlobal(Array &$ref, Node $node, SourceUnit $unit, $filename, $test)
    {
        $ref['DefUnit'] = $unit->getPackageName($ref['DefPath']);
        $ref['DefUnitType'] = $unit->getType();
        $ref['DefRepo'] = $unit->getRepository($ref['DefUnit']);
        $ref['File'] = $filename;
        $ref['Start'] = $node->getAttribute('startFilePos');
        $ref['End'] = $node->getAttribute('endFilePos');
    }
}
