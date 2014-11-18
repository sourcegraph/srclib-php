<?php

namespace Foo {
    function bar() {}
}

namespace Qux {
    use function Foo\bar;
    bar();
}

