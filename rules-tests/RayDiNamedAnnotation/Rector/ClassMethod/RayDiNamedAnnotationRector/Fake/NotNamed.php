<?php

use Ray\Di\Di\Named;

class NotNamed
{
    /**
     * @Named("a=foo,b=bar")
     * @Foo
     */
    public function __construct(int $a, int $b, $notNamed)
    {
    }
}
