<?php

namespace Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\Fixture;

use BEAR\Resource\ResourceObject;

class SkipAlreadyStaticClass extends ResourceObject
{
    public function onGet(int $id): static
    {
        return $this;
    }
}

?>
