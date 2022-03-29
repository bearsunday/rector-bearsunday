<?php

namespace Rector\Tests\RayNamedAnnotation\Rector\Class_\RayNamedAnnotationRector\Fixture;

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

namespace Rector\Tests\RayNamedAnnotation\Rector\Class_\RayNamedAnnotationRector\Fixture;

/**
     * @Foo
     */
    public function __construct(#[Named('foo') int $a, #[Named('bar') int $b)
    {
    }

?>
