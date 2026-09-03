<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

use BEAR\Sunday\Inject\PsrLoggerInject;

class PsrLoggerInjectClass
{
    use PsrLoggerInject;

    public function doSomething(): void
    {
        $this->logger->info('hello');
    }
}

?>
-----
<?php

namespace Rector\Tests\TraitInjection\Rector\Class_\TraitToConstructorInjectionRector\Fixture;

class PsrLoggerInjectClass
{
    public function __construct(private readonly \Psr\Log\LoggerInterface $logger)
    {
    }

    public function doSomething(): void
    {
        $this->logger->info('hello');
    }
}

?>
