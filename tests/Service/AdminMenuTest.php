<?php

namespace AlipayBillBundle\Tests\Service;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Service\AdminMenu;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    
    private LinkGeneratorInterface&MockObject $linkGenerator;
    
    private ItemInterface&MockObject $item;
    
    private ItemInterface&MockObject $alipayBillMenu;
    
    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->item = $this->createMock(ItemInterface::class);
        $this->alipayBillMenu = $this->createMock(ItemInterface::class);
        
        $this->adminMenu = new AdminMenu($this->linkGenerator);
    }
    
    public function testInvokeCreatesNewMenu(): void
    {
        // 模拟没有子菜单的情况
        $this->item->expects($this->exactly(2))
            ->method('getChild')
            ->with('支付宝账单')
            ->willReturnOnConsecutiveCalls(null, $this->alipayBillMenu);
            
        $this->item->expects($this->once())
            ->method('addChild')
            ->with('支付宝账单')
            ->willReturn($this->alipayBillMenu);
            
        // 模拟链接生成
        $this->linkGenerator->expects($this->exactly(2))
            ->method('getCurdListPage')
            ->willReturnMap([
                [Account::class, 'https://example.com/admin/account'],
                [BillUrl::class, 'https://example.com/admin/bill-url'],
            ]);
            
        // 模拟菜单项添加
        $accountMenu = $this->createMock(ItemInterface::class);
        $billMenu = $this->createMock(ItemInterface::class);
        
        $this->alipayBillMenu->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnMap([
                ['账号管理', $accountMenu],
                ['账单管理', $billMenu],
            ]);
            
        $accountMenu->expects($this->once())
            ->method('setUri')
            ->with('https://example.com/admin/account')
            ->willReturn($accountMenu);
            
        $accountMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-user-circle')
            ->willReturn($accountMenu);
            
        $billMenu->expects($this->once())
            ->method('setUri')
            ->with('https://example.com/admin/bill-url')
            ->willReturn($billMenu);
            
        $billMenu->expects($this->once())
            ->method('setAttribute')
            ->with('icon', 'fas fa-file-invoice')
            ->willReturn($billMenu);
            
        ($this->adminMenu)($this->item);
    }
    
    public function testInvokeUsesExistingMenu(): void
    {
        // 模拟已有子菜单的情况
        $this->item->expects($this->exactly(2))
            ->method('getChild')
            ->with('支付宝账单')
            ->willReturn($this->alipayBillMenu);
            
        $this->item->expects($this->never())
            ->method('addChild');
            
        // 模拟链接生成
        $this->linkGenerator->expects($this->exactly(2))
            ->method('getCurdListPage')
            ->willReturnMap([
                [Account::class, 'https://example.com/admin/account'],
                [BillUrl::class, 'https://example.com/admin/bill-url'],
            ]);
            
        // 模拟菜单项添加
        $accountMenu = $this->createMock(ItemInterface::class);
        $billMenu = $this->createMock(ItemInterface::class);
        
        $this->alipayBillMenu->expects($this->exactly(2))
            ->method('addChild')
            ->willReturnMap([
                ['账号管理', $accountMenu],
                ['账单管理', $billMenu],
            ]);
            
        $accountMenu->expects($this->once())
            ->method('setUri')
            ->willReturn($accountMenu);
            
        $accountMenu->expects($this->once())
            ->method('setAttribute')
            ->willReturn($accountMenu);
            
        $billMenu->expects($this->once())
            ->method('setUri')
            ->willReturn($billMenu);
            
        $billMenu->expects($this->once())
            ->method('setAttribute')
            ->willReturn($billMenu);
            
        ($this->adminMenu)($this->item);
    }
}