<?php

namespace Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\Fixture;

interface FooInterface {}

class SkipNonInjectSetterClass
{
    private FooInterface $foo;

    public function setFoo(FooInterface $foo): void
    {
        $this->foo = $foo;
    }
}

?>
