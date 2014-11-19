<?php

namespace Foo {
    /**
     * Documentation
     */
    function bar() {}
}

namespace Qux {
    use function Foo\bar;

    /* doc */
    bar();
}

