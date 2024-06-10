<?php

namespace Utils\Rector\Tests\Rector\RayQueryModuleRector\Fixture;

use Ray\Di\Di\Named;
use Ray\Query\QueryInterface;
use Ray\Query\RowListInterface;

class FakeItem implements QueryInterface
{
    public function __construct(
        #[Named('todo_item_by_id')] private RowListInterface $todo
    ){
    }
}
?>
-----
<?php

namespace Utils\Rector\Tests\Rector\RayQueryModuleRector\Fixture;

use Ray\Di\Di\Named;
use Ray\Query\QueryInterface;
use Ray\Query\RowListInterface;

class FakeItem implements QueryInterface
{
    public function __construct(
        #[\Ray\Query\Annotation\Sql('todo_item_by_id')] private RowListInterface $todo
    ){
    }
}
?>
