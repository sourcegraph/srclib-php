<?php

namespace Sourcegraph\PHP\Grapher;

use Sourcegraph\PHP\Grapher;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class DefExtractor
{
    public function extract($filename, Array $nodes, $test = false)
    {
        $defs = [];
        foreach ($nodes as $node) {
            if ($def = $this->processNode($node, $filename, $test)) {
                $defs[] = $def;
            }
        }

        return $defs;
    }

    private function processNode(Node $node, $filename, $test)
    {
        switch (true) {
            case $node instanceof Expr\Assign:
                $def = $this->extractExprAssign($node);
                break;
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

    protected function extractExprAssign(Expr\Assign $node)
    {
        return [
            'Kind' => Grapher::KIND_ASSIGN,
            'Name' => $node->var->name
        ];
    }

    protected function extractStmtClass(Stmt\Class_ $node)
    {
        return [
            'Kind' => Grapher::KIND_CLASS,
            'Name' => $node->name,
            'TreePath' => $node->namespacedName->toString('/'),
        ];
    }

    protected function extractStmtTrait(Stmt\Trait_ $node)
    {
        return [
            'Kind' => Grapher::KIND_TRAIT,
            'Name' => $node->name,
            'TreePath' => $node->namespacedName->toString('/'),
        ];
    }

    protected function extractStmtInterface(Stmt\Interface_ $node)
    {
        return [
            'Kind' => Grapher::KIND_INTERFACE,
            'Name' => $node->name,
            'TreePath' => $node->namespacedName->toString('/'),
        ];
    }


    protected function extractStmtFunction(Stmt\Function_ $node)
    {
        return [
            'Kind' => Grapher::KIND_FUNCTION,
            'Name' => $node->name,
            'TreePath' => $node->namespacedName->toString('/'),
        ];
    }

    protected function extractStmtClassMethod(Stmt\ClassMethod $node)
    {
        return [
            'Kind' => Grapher::KIND_METHOD,
            'Name' => $node->name,
            'TreePath' => $node->namespacedName->toString('/'),
            'Exported' => $node->isPublic()
        ];
    }

    protected function extractStmtProperty(Stmt\Property $node)
    {
        return [
            'Kind' => Grapher::KIND_PROPERTY,
            'Name' => $node->props[0]->name,
            'TreePath' => $node->namespacedName->toString('/'),
            'Exported' => $node->type == Stmt\Class_::MODIFIER_PUBLIC
        ];
    }

    protected function extractStmtConst(Stmt\Const_ $node)
    {
        return [
            'Kind' => Grapher::KIND_CONSTANT,
            'Name' => $node->consts[0]->name,
            'TreePath' => $node->consts[0]->namespacedName->toString('/'),
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
        $def['DefStart'] = $node->getAttribute('startPos');
        $def['DefEnd'] = $node->getAttribute('endPos');

        if (!isset($def['Exported'])) {
            $def['Exported'] = true;
        }
    }
}
