<?php

namespace Foo;
use DateTime;

const QUX = 'test';

/**
 * Command that places bundle web assets into a given directory.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Bar extends Foo implements Baz
{
    const BAZ = 'baz';
    use Qux;

    /* Escapes "<" special char in given text. */
    public $publicProperty;
    private $privateProperty;
    protected $protectedProperty;

    public function __construct(Foo $foo) {
        $this->foo = $foo;
    }

    /**
     * Escapes "<" special char in given text.
     *
     * @param string $text Text to escape
     *
     * @return string Escaped text
     */
    public function publicMethod() {}
    protected function protectedMethod() {}
    private function privateMethod() {}
}

class Foo {}

/**
 * Setup the layout used by the controller.
 */
trait Qux {
    public function Bar() {}
}

interface Baz
{
    public function publicMethod();
}

function baz(DateTime $bar, $qux = QUX) {
    $foo = Bar::BAZ;
}


$bar = new Bar(new Foo());
