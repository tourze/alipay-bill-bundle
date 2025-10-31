<?php

namespace AlipayBillBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

class AlipayBillExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
