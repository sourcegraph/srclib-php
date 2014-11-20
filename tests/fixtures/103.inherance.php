<?php

namespace Foo {
    class Baz {}
    interface Qux {}
    interface Bar {}
    trait Foo {}
    trait Fuz {}
}

namespace Baz {
    class Foo extends \Foo\Baz implements \Foo\Qux, \Foo\Bar {
        use \Foo\Foo, \Foo\Fuz;
    }
}
