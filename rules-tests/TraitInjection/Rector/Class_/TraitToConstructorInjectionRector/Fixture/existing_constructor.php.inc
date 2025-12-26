<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

use BEAR\Resource\ResourceInject;

interface FooInterface {}

class ExistingConstructorClass
{
    use ResourceInject;

    public function __construct(
        private readonly FooInterface $foo
    ) {
    }
}

?>
-----
<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

use BEAR\Resource\ResourceInject;

interface FooInterface {}

class ExistingConstructorClass
{
    public function __construct(
        private readonly FooInterface $foo, private readonly \BEAR\Resource\ResourceInterface $resource
    ) {
    }
}

?>
