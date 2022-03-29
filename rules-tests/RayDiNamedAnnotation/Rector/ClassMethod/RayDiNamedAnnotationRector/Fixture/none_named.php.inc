<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class NoneNamed
{
    /**
     * @Foo
     */
    public function __construct(int $a, int $b)
    {
    }
}

?>
-----
<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class NoneNamed
{
    /**
     * @Foo
     */
    public function __construct(int $a, int $b)
    {
    }
}

?>
