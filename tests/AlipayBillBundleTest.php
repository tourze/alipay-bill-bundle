<?php

namespace AlipayBillBundle\Tests;

use AlipayBillBundle\AlipayBillBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AlipayBillBundleTest extends TestCase
{
    public function testInstanceOfBundle()
    {
        $bundle = new AlipayBillBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testGetPath()
    {
        $bundle = new AlipayBillBundle();
        $path = $bundle->getPath();

        $this->assertStringContainsString('alipay-bill-bundle', $path);
        $this->assertDirectoryExists($path);
    }
}
