<?php
abstract class A {
    abstract function x();
}
interface B {
    function y();
}
abstract class C extends A implements B {
    function y() {}
}
abstract class D extends A {
    function y() {}
}
class E extends D implements B {
    function x() {}
}