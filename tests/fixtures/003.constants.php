<?php

namespace Foo {
    /* doc */
    const QUX = 'test';

    /* doc */
    define('BAR', 'bar');
}

namespace Qux {
    use const Foo\BAR;
    use const Foo\QUX;
}
