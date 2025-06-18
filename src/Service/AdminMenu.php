<?php

namespace AlipayBillBundle\Service;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

/**
 * 支付宝账单菜单服务
 */
class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private readonly LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        if (null === $item->getChild('支付宝账单')) {
            $item->addChild('支付宝账单');
        }

        $alipayBillMenu = $item->getChild('支付宝账单');
        
        // 账号管理菜单
        $alipayBillMenu->addChild('账号管理')
            ->setUri($this->linkGenerator->getCurdListPage(Account::class))
            ->setAttribute('icon', 'fas fa-user-circle');
        
        // 账单管理菜单
        $alipayBillMenu->addChild('账单管理')
            ->setUri($this->linkGenerator->getCurdListPage(BillUrl::class))
            ->setAttribute('icon', 'fas fa-file-invoice');
    }
}
