<?php
namespace Qux;
use Bar\Qux as Bar;

echo "hola";

class Foo {}
function test(Array $test, Foo $bar) {
    echo "bar";
    $test = new Foo();
}

$test = new Foo();
$test = new Bar\Foo($foo);

