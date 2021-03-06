<?php

namespace Sourcegraph\PHP\Grapher;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use Sourcegraph\PHP\SourceUnit;
use Sourcegraph\PHP\Grapher;

class DefExtractor implements Extractor
{
    public function extract(SourceUnit $unit, $filename, Array $nodes, $test = false)
    {
        $defs = [];
        foreach ($nodes as $node) {
            if ($def = $this->buildDef($node, $filename, $test)) {
                $defs[] = $def;
            }
        }

        return $defs;
    }

    private function buildDef(Node $node, $filename, $test)
    {
        switch (true) {
            case $node instanceof Stmt\Class_:
                $def = $this->extractStmtClass($node);
                break;
            case $node instanceof Stmt\Trait_:
                $def = $this->extractStmtTrait($node);
                break;
            case $node instanceof Stmt\Interface_:
                $def = $this->extractStmtInterface($node);
                break;
            case $node instanceof Stmt\Function_:
                $def = $this->extractStmtFunction($node);
                break;
            case $node instanceof Stmt\ClassMethod:
                $def = $this->extractStmtClassMethod($node);
                break;
            case $node instanceof Stmt\Property:
                $def = $this->extractStmtProperty($node);
                break;
            case $node instanceof Stmt\Const_:
                $def = $this->extractStmtConst($node);
                break;
            case $node instanceof Expr\FuncCall:
                $def = $this->extractExprConstFromFuncCall($node);
                break;
            default:
                return;
        }

        if ($def) {
            $this->applyGlobal($def, $node, $filename, $test);
        }

        return $def;
    }

    protected function extractStmtClass(Stmt\Class_ $node)
    {
        return [
            'Kind' => Grapher::KIND_CLASS,
            'Name' => $node->name,
            'Path' => $node->namespacedName->toString('/'),
        ];
    }

    protected function extractStmtTrait(Stmt\Trait_ $node)
    {
        return [
            'Kind' => Grapher::KIND_TRAIT,
            'Name' => $node->name,
            'Path' => $node->namespacedName->toString('/'),
        ];
    }

    protected function extractStmtInterface(Stmt\Interface_ $node)
    {
        return [
            'Kind' => Grapher::KIND_INTERFACE,
            'Name' => $node->name,
            'Path' => $node->namespacedName->toString('/'),
        ];
    }


    protected function extractStmtFunction(Stmt\Function_ $node)
    {
        return [
            'Kind' => Grapher::KIND_FUNCTION,
            'Name' => $node->name,
            'Path' => $node->namespacedName->toString('/'),
        ];
    }

    protected function extractStmtClassMethod(Stmt\ClassMethod $node)
    {
        return [
            'Kind' => Grapher::KIND_METHOD,
            'Name' => $node->name,
            'Path' => $node->namespacedName->toString('/'),
            'Exported' => $node->isPublic()
        ];
    }

    protected function extractStmtProperty(Stmt\Property $node)
    {
        return [
            'Kind' => Grapher::KIND_PROPERTY,
            'Name' => $node->props[0]->name,
            'Path' => $node->namespacedName->toString('/'),
            'Exported' => $node->type == Stmt\Class_::MODIFIER_PUBLIC
        ];
    }

    protected function extractStmtConst(Stmt\Const_ $node)
    {
        return [
            'Kind' => Grapher::KIND_CONSTANT,
            'Name' => $node->consts[0]->name,
            'Path' => $node->consts[0]->namespacedName->toString('/'),
        ];
    }

    protected function extractExprConstFromFuncCall(Expr\FuncCall $node)
    {
        if ((string) $node->name != 'define') {
            return null;
        }

        return [
            'Kind' => Grapher::KIND_CONSTANT,
            'Name' => $node->args[0]->value->value,
        ];
    }

    protected function applyGlobal(Array &$def, Node $node, $filename, $test)
    {
        $def['File'] = $filename;
        $def['Test'] = $test;
        $def['DefStart'] = $node->getAttribute('startFilePos');
        $def['DefEnd'] = $node->getAttribute('endFilePos');

        if (!isset($def['Exported'])) {
            $def['Exported'] = true;
        }
    }
}
