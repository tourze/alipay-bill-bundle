<?php

namespace AlipayBillBundle\Tests\DependencyInjection;

use AlipayBillBundle\DependencyInjection\AlipayBillExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(AlipayBillExtension::class)]
final class AlipayBillExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
