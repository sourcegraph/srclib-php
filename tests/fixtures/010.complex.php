<?php

namespace Foo;

const QUX = 'test';

class Bar implements Baz
{
    use Qux;

    public $publicProperty;
    private $privateProperty;
    protected $protectedProperty;

    public function __construct($foo) {
        $this->foo = $foo;
    }

    public function publicMethod() {}
    protected function protectedMethod() {}
    private function privateMethod() {}
}

trait Qux {
    public function Bar() {}
}

interface Baz
{
    public function publicMethod();
}

function baz() {}
