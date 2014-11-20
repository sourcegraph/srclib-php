<?php

namespace Foo {
    const BAZ = 'baz';
    class Bar {}
    class Qux {
        const QUX = 'qux';
    }

    function qux(Bar $bar, $baz = BAZ) {}
}

namespace Baz {
    use Foo\Qux;

    class Baz
    {
        public function __construct(\Foo\Bar $bar, Qux $qux) {}
        private function withDefaultClassConstant($foo = Qux::QUX) {}
    }
}
