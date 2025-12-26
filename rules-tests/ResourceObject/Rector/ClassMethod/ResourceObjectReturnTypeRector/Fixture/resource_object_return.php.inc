<?php

namespace Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\Fixture;

use BEAR\Resource\ResourceObject;

class Article extends ResourceObject
{
    public function onGet(int $id): ResourceObject
    {
        return $this;
    }

    public function onPost(string $title): ResourceObject
    {
        return $this;
    }
}

?>
-----
<?php

namespace Rector\Tests\ResourceObject\Rector\ClassMethod\ResourceObjectReturnTypeRector\Fixture;

use BEAR\Resource\ResourceObject;

class Article extends ResourceObject
{
    public function onGet(int $id): static
    {
        return $this;
    }

    public function onPost(string $title): static
    {
        return $this;
    }
}

?>
