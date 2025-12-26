<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

trait SomeOtherTrait
{
    public function someMethod(): void
    {
    }
}

class SkipNonMatchingTraitClass
{
    use SomeOtherTrait;
}

?>
