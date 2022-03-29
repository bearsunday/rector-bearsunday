<?php

namespace Rector\Tests\RayDiNamedAnnotation\Rector\ClassMethod\RayDiNamedAnnotationRector\Fixture;

class SomeClass
{
    /**
     * @Named("a=foo,b=bar")
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

class SomeClass
{
    /**
     * @Foo
     */
    public function __construct(#[\Ray\Di\Di\Named('foo')] int $a, #[\Ray\Di\Di\Named('bar')] int $b)
    {
    }
}

?>
