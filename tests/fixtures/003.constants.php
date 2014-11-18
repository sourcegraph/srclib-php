<?php

namespace Foo {
    const QUX = 'test';
    define('BAR', 'bar');
}

namespace Qux {
    use const Foo\BAR;
    use const Foo\QUX;
}
