<?php

namespace AlipayBillBundle\Tests\Entity;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Enum\BillType;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(BillUrl::class)]
final class BillUrlTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): object
    {
        return new BillUrl();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        $account = new Account();
        $account->setId('123456789');
        $account->setName('测试账号');
        $account->setAppId('2021000000000000');

        yield 'id' => ['id', 123];
        yield 'account' => ['account', $account];
        yield 'type' => ['type', BillType::signcustomer];
        yield 'typeMerchant' => ['type', BillType::merchant_act];
        yield 'typeSettlement' => ['type', BillType::settlementMerge];
        yield 'date' => ['date', new \DateTime('2023-01-01')];
        yield 'downloadUrl' => ['downloadUrl', 'https://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=trade&userId=2088123456789&fileType=csv&bizDates=20230101&downloadFileName=20230101.csv&fileId=xxx'];
        yield 'localFile' => ['localFile', 'alipay-bill/20230101-123456.zip'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    public function testDefaultType(): void
    {
        $billUrl = $this->createEntity();
        $this->assertInstanceOf(BillUrl::class, $billUrl);

        // 默认类型应为trade
        $this->assertSame(BillType::trade, $billUrl->getType());
    }
}
