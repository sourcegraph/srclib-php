<?php

namespace Foo {
    class Bar extends Foo
    {
        public function __construct() {}
        protected function protectedMethod() {}
        private function privateMethod() {}
    }

    class Foo
    {
        public function extendedMethod() {}
    }
}
