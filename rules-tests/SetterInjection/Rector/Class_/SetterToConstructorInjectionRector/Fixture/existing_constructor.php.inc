<?php

namespace Rector\Tests\SetterInjection\Rector\Class_\SetterToConstructorInjectionRector\Fixture;

use Ray\Di\Di\Inject;

interface FooInterface {}
interface BarInterface {}

class ExistingConstructorClass
{
    private FooInterface $foo;

    public function __construct(
        private readonly BarInterface $bar
    ) {
    }

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
interface BarInterface {}

class ExistingConstructorClass
{
    public function __construct(
        private readonly BarInterface $bar, private readonly FooInterface $foo
    ) {
    }
}

?>
