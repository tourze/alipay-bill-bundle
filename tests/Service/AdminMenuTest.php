<?php

namespace AlipayBillBundle\Tests\Service;

use AlipayBillBundle\Service\AdminMenu;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function onSetUp(): void
    {
        // 设置测试环境
    }

    public function testServiceCreation(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testImplementsMenuProviderInterface(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    public function testInvokeAddsMenuItems(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        $adminMenu->__invoke($rootItem);

        // 验证菜单结构
        $alipayBillMenu = $rootItem->getChild('支付宝账单');
        self::assertNotNull($alipayBillMenu);

        $accountMenu = $alipayBillMenu->getChild('账号管理');
        self::assertNotNull($accountMenu);
        self::assertEquals('fas fa-user-circle', $accountMenu->getAttribute('icon'));

        $billMenu = $alipayBillMenu->getChild('账单管理');
        self::assertNotNull($billMenu);
        self::assertEquals('fas fa-file-invoice', $billMenu->getAttribute('icon'));
    }

    public function testInvokeHandlesExistingMenu(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 预先创建支付宝账单菜单
        $existingMenu = $rootItem->addChild('支付宝账单');

        $adminMenu->__invoke($rootItem);

        // 验证使用了已存在的菜单
        $alipayBillMenu = $rootItem->getChild('支付宝账单');
        self::assertSame($existingMenu, $alipayBillMenu);

        // 验证子菜单仍然被添加
        self::assertNotNull($alipayBillMenu->getChild('账号管理'));
        self::assertNotNull($alipayBillMenu->getChild('账单管理'));
    }

    public function testInvokeMultipleCallsDoesNotDuplicate(): void
    {
        /** @var AdminMenu $adminMenu */
        $adminMenu = self::getService(AdminMenu::class);

        $factory = new MenuFactory();
        $rootItem = $factory->createItem('root');

        // 调用两次
        $adminMenu->__invoke($rootItem);
        $adminMenu->__invoke($rootItem);

        // 验证只有一个支付宝账单菜单
        $alipayBillMenu = $rootItem->getChild('支付宝账单');
        self::assertNotNull($alipayBillMenu);

        // 验证子菜单正常存在（不重复）
        self::assertCount(2, $alipayBillMenu->getChildren());
        self::assertNotNull($alipayBillMenu->getChild('账号管理'));
        self::assertNotNull($alipayBillMenu->getChild('账单管理'));
    }
}
