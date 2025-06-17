<?php

namespace AlipayBillBundle\Tests\Entity;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Enum\BillType;
use PHPUnit\Framework\TestCase;

class BillUrlTest extends TestCase
{
    private BillUrl $billUrl;
    private Account $account;

    protected function setUp(): void
    {
        $this->billUrl = new BillUrl();
        $this->account = new Account();
        $this->account->setId('123456789');
        $this->account->setName('测试账号');
        $this->account->setAppId('2021000000000000');
    }

    public function testGetterAndSetterForId()
    {
        $this->assertEquals(0, $this->billUrl->getId());
        
        $id = 123;
        $this->billUrl->setId($id);
        $this->assertSame($id, $this->billUrl->getId());
    }

    public function testGetterAndSetterForAccount()
    {
        $this->billUrl->setAccount($this->account);
        $this->assertSame($this->account, $this->billUrl->getAccount());
    }

    public function testGetterAndSetterForType()
    {
        // 默认类型应为trade
        $this->assertSame(BillType::trade, $this->billUrl->getType());
        
        $this->billUrl->setType(BillType::signcustomer);
        $this->assertSame(BillType::signcustomer, $this->billUrl->getType());
        
        $this->billUrl->setType(BillType::merchant_act);
        $this->assertSame(BillType::merchant_act, $this->billUrl->getType());
        
        $this->billUrl->setType(BillType::settlementMerge);
        $this->assertSame(BillType::settlementMerge, $this->billUrl->getType());
    }

    public function testGetterAndSetterForDate()
    {
        $date = new \DateTime('2023-01-01');
        $this->billUrl->setDate($date);
        $this->assertSame($date, $this->billUrl->getDate());
    }

    public function testGetterAndSetterForDownloadUrl()
    {
        $downloadUrl = 'https://dwbillcenter.alipay.com/downloadBillFile.resource?bizType=trade&userId=2088123456789&fileType=csv&bizDates=20230101&downloadFileName=20230101.csv&fileId=xxx';
        $this->billUrl->setDownloadUrl($downloadUrl);
        $this->assertSame($downloadUrl, $this->billUrl->getDownloadUrl());
    }

    public function testGetterAndSetterForLocalFile()
    {
        $this->assertNull($this->billUrl->getLocalFile());
        
        $localFile = 'alipay-bill/20230101-123456.zip';
        $this->billUrl->setLocalFile($localFile);
        $this->assertSame($localFile, $this->billUrl->getLocalFile());
    }

    public function testGetterAndSetterForCreateTime()
    {
        $this->assertNull($this->billUrl->getCreateTime());
        
        $createTime = new \DateTimeImmutable();
        $this->billUrl->setCreateTime($createTime);
        $this->assertSame($createTime, $this->billUrl->getCreateTime());
    }

    public function testGetterAndSetterForUpdateTime()
    {
        $this->assertNull($this->billUrl->getUpdateTime());
        
        $updateTime = new \DateTimeImmutable();
        $this->billUrl->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->billUrl->getUpdateTime());
    }
} 