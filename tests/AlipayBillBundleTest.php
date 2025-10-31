<?php

declare(strict_types=1);

namespace AlipayBillBundle\Tests;

use AlipayBillBundle\AlipayBillBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayBillBundle::class)]
#[RunTestsInSeparateProcesses]
final class AlipayBillBundleTest extends AbstractBundleTestCase
{
}
