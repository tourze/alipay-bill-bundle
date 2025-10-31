<?php

namespace AlipayBillBundle\Tests\Enum;

use AlipayBillBundle\Enum\BillType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(BillType::class)]
final class BillTypeTest extends AbstractEnumTestCase
{
    public function testEnumValues(): void
    {
        $this->assertSame('trade', BillType::trade->value);
        $this->assertSame('signcustomer', BillType::signcustomer->value);
        $this->assertSame('merchant_act', BillType::merchant_act->value);
        $this->assertSame('settlementMerge', BillType::settlementMerge->value);
    }

    public function testGetLabel(): void
    {
        $this->assertSame('商户基于支付宝交易收单的业务账单', BillType::trade->getLabel());
        $this->assertSame('基于商户支付宝余额收入及支出等资金变动的账务账单', BillType::signcustomer->getLabel());
        $this->assertSame('营销活动账单，包含营销活动的发放，核销记录', BillType::merchant_act->getLabel());
        $this->assertSame('每日结算到卡的资金对应的明细，下载内容包含批次结算到卡明细文件', BillType::settlementMerge->getLabel());
    }

    public function testToArray(): void
    {
        $expected = [
            'value' => 'trade',
            'label' => '商户基于支付宝交易收单的业务账单',
        ];

        $this->assertSame($expected, BillType::trade->toArray());
    }

    public function testGenOptions(): void
    {
        $options = BillType::genOptions();
        $this->assertCount(4, $options);

        // 测试第一个选项的结构
        $this->assertArrayHasKey('label', $options[0]);
        $this->assertArrayHasKey('text', $options[0]);
        $this->assertArrayHasKey('value', $options[0]);
        $this->assertArrayHasKey('name', $options[0]);

        // 测试特定选项
        $tradeOption = $options[0];
        $this->assertSame('trade', $tradeOption['value']);
        $this->assertSame('商户基于支付宝交易收单的业务账单', $tradeOption['label']);
        $this->assertSame('商户基于支付宝交易收单的业务账单', $tradeOption['text']);
        $this->assertSame('商户基于支付宝交易收单的业务账单', $tradeOption['name']);
    }
}
