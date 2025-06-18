<?php

namespace AlipayBillBundle\Tests\Enum;

use AlipayBillBundle\Enum\BillType;
use PHPUnit\Framework\TestCase;

class BillTypeTest extends TestCase
{
    public function testEnumValues()
    {
        $this->assertSame('trade', BillType::trade->value);
        $this->assertSame('signcustomer', BillType::signcustomer->value);
        $this->assertSame('merchant_act', BillType::merchant_act->value);
        $this->assertSame('settlementMerge', BillType::settlementMerge->value);
    }

    public function testGetLabel()
    {
        $this->assertSame('商户基于支付宝交易收单的业务账单', BillType::trade->getLabel());
        $this->assertSame('基于商户支付宝余额收入及支出等资金变动的账务账单', BillType::signcustomer->getLabel());
        $this->assertSame('营销活动账单，包含营销活动的发放，核销记录', BillType::merchant_act->getLabel());
        $this->assertSame('每日结算到卡的资金对应的明细，下载内容包含批次结算到卡明细文件', BillType::settlementMerge->getLabel());
    }

} 