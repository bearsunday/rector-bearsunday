<?php

namespace Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\Fixture;

use BEAR\Resource\ResourceObject;

class User extends ResourceObject
{
    public function onGet(int $id): self
    {
        return $this;
    }
}

?>
-----
<?php

namespace Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\Fixture;

use BEAR\Resource\ResourceObject;

class User extends ResourceObject
{
    public function onGet(int $id): static
    {
        return $this;
    }
}

?>
