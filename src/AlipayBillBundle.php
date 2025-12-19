<?php

namespace AlipayBillBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HttpClientBundle\HttpClientBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;
use Tourze\FlysystemBundle\FlysystemBundle;
use Tourze\Symfony\RuntimeContextBundle\RuntimeContextBundle;

class AlipayBillBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
            HttpClientBundle::class => ['all' => true],
            RuntimeContextBundle::class => ['all' => true],
            FlysystemBundle::class => ['all' => true],
        ];
    }
}
