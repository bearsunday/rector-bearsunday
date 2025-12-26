<?php

namespace Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\Fixture;

class SkipNonResourceClass
{
    public function onGet(int $id): self
    {
        return $this;
    }
}

?>
