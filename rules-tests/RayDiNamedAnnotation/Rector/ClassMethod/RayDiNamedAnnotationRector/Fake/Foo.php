<?php

use Ray\Di\Di\Named;

class SomeClass
{
    /**
     * @Named("a=foo,b=bar")
     * @Foo
     */
    public function __construct(#[Named()] int $a, int $b)
    {
    }
}
