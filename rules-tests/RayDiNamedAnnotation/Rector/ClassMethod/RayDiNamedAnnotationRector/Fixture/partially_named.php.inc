<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

use Ray\Di\Di\Named;

class PartiallyNamed
{
    /**
     * @Named("a=foo,b=bar")
     * @Foo
     */
    public function __construct(int $a, int $b, int $notNamed)
    {
    }
}

?>
-----
<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

use Ray\Di\Di\Named;

class PartiallyNamed
{
    /**
     * @Foo
     */
    public function __construct(#[\Ray\Di\Di\Named('foo')] int $a, #[\Ray\Di\Di\Named('bar')] int $b, int $notNamed)
    {
    }
}

?>
