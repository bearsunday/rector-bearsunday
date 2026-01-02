<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

use Ray\Di\Di\Named;

class SingleParamClass
{
    /**
     * @Named("foo")
     */
    public function __construct(int $a)
    {
    }
}

?>
-----
<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

use Ray\Di\Di\Named;

class SingleParamClass
{
    public function __construct(#[\Ray\Di\Di\Named('foo')]
    int $a)
    {
    }
}

?>
