<?php

namespace Foo {
    const BAR = 'bar';
    function qux() {}
    class Baz {}
}

namespace Baz {
    use const Foo\BAR;
    use function Foo\qux;
    use Foo\Baz;
}
