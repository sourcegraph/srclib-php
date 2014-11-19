<?php

namespace Foo {
    const FOO = 'foo';
    class Baz {
        const BAR = 'bar';
    }
}

namespace Baz {
    $baz = new \Foo\Baz();
    $bar = Baz::BAR;
    $foo = FOO;
}
