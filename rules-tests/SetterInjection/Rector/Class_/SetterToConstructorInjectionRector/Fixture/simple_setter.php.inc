<?php

namespace Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\Fixture;

use Ray\Di\Di\Inject;

interface FooInterface {}

class SimpleSetterClass
{
    private FooInterface $foo;

    #[Inject]
    public function setFoo(FooInterface $foo): void
    {
        $this->foo = $foo;
    }
}

?>
-----
<?php

namespace Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\Fixture;

use Ray\Di\Di\Inject;

interface FooInterface {}

class SimpleSetterClass
{
    public function __construct(private readonly FooInterface $foo)
    {
    }
}

?>
