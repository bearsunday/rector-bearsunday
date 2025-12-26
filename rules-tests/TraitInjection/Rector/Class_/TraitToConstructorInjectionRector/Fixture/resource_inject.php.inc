<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

use BEAR\Resource\ResourceInject;

class ResourceInjectClass
{
    use ResourceInject;

    public function doSomething(): void
    {
        $this->resource->get('app://self/user');
    }
}

?>
-----
<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

use BEAR\Resource\ResourceInject;

class ResourceInjectClass
{
    public function __construct(private readonly \BEAR\Resource\ResourceInterface $resource)
    {
    }

    public function doSomething(): void
    {
        $this->resource->get('app://self/user');
    }
}

?>
