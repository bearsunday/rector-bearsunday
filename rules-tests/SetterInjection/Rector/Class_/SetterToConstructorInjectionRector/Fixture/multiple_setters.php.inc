<?php

namespace Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\Fixture;

use Ray\Di\Di\Inject;

interface FooInterface {}
interface BarInterface {}

class MultipleSettersClass
{
    private FooInterface $foo;
    private BarInterface $bar;

    #[Inject]
    public function setFoo(FooInterface $foo): void
    {
        $this->foo = $foo;
    }

    #[Inject]
    public function setBar(BarInterface $bar): void
    {
        $this->bar = $bar;
    }
}

?>
-----
<?php

namespace Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\Fixture;

use Ray\Di\Di\Inject;

interface FooInterface {}
interface BarInterface {}

class MultipleSettersClass
{
    public function __construct(private readonly FooInterface $foo, private readonly BarInterface $bar)
    {
    }
}

?>
