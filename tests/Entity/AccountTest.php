<?php

namespace AlipayBillBundle\Tests\Entity;

use AlipayBillBundle\Entity\Account;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    private Account $account;

    protected function setUp(): void
    {
        $this->account = new Account();
    }

    public function testGetterAndSetterForId()
    {
        $this->assertNull($this->account->getId());

        $id = '123456789';
        $this->account->setId($id);
        $this->assertSame($id, $this->account->getId());
    }

    public function testGetterAndSetterForName()
    {
        $name = '测试账号';
        $this->account->setName($name);
        $this->assertSame($name, $this->account->getName());
    }

    public function testGetterAndSetterForAppId()
    {
        $this->assertNull($this->account->getAppId());

        $appId = '2022123456789';
        $this->account->setAppId($appId);
        $this->assertSame($appId, $this->account->getAppId());
    }

    public function testGetterAndSetterForRsaPrivateKey()
    {
        $this->assertNull($this->account->getRsaPrivateKey());

        $rsaPrivateKey = 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC7VJTUt9Us8cKj';
        $this->account->setRsaPrivateKey($rsaPrivateKey);
        $this->assertSame($rsaPrivateKey, $this->account->getRsaPrivateKey());
    }

    public function testGetterAndSetterForRsaPublicKey()
    {
        $this->assertNull($this->account->getRsaPublicKey());

        $rsaPublicKey = 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAut9evKRuHJ/2QNfDlLwv';
        $this->account->setRsaPublicKey($rsaPublicKey);
        $this->assertSame($rsaPublicKey, $this->account->getRsaPublicKey());
    }

    public function testGetterAndSetterForValid()
    {
        $this->assertFalse($this->account->isValid());

        $this->account->setValid(true);
        $this->assertTrue($this->account->isValid());

        $this->account->setValid(false);
        $this->assertFalse($this->account->isValid());
    }

    public function testGetterAndSetterForCreatedBy()
    {
        $this->assertNull($this->account->getCreatedBy());

        $createdBy = 'admin';
        $this->account->setCreatedBy($createdBy);
        $this->assertSame($createdBy, $this->account->getCreatedBy());
    }

    public function testGetterAndSetterForUpdatedBy()
    {
        $this->assertNull($this->account->getUpdatedBy());

        $updatedBy = 'admin';
        $this->account->setUpdatedBy($updatedBy);
        $this->assertSame($updatedBy, $this->account->getUpdatedBy());
    }

    public function testGetterAndSetterForCreatedFromIp()
    {
        $this->assertNull($this->account->getCreatedFromIp());

        $createdFromIp = '127.0.0.1';
        $this->account->setCreatedFromIp($createdFromIp);
        $this->assertSame($createdFromIp, $this->account->getCreatedFromIp());
    }

    public function testGetterAndSetterForUpdatedFromIp()
    {
        $this->assertNull($this->account->getUpdatedFromIp());

        $updatedFromIp = '127.0.0.1';
        $this->account->setUpdatedFromIp($updatedFromIp);
        $this->assertSame($updatedFromIp, $this->account->getUpdatedFromIp());
    }

    public function testGetterAndSetterForCreateTime()
    {
        $this->assertNull($this->account->getCreateTime());

        $createTime = new \DateTime();
        $this->account->setCreateTime($createTime);
        $this->assertSame($createTime, $this->account->getCreateTime());
    }

    public function testGetterAndSetterForUpdateTime()
    {
        $this->assertNull($this->account->getUpdateTime());

        $updateTime = new \DateTime();
        $this->account->setUpdateTime($updateTime);
        $this->assertSame($updateTime, $this->account->getUpdateTime());
    }

    public function testToString()
    {
        $this->assertSame('', (string)$this->account);

        $name = '测试账号';
        $this->account->setName($name);

        // 没有ID时应返回空字符串
        $this->assertSame('', (string)$this->account);

        // 设置ID后应返回名称
        $this->account->setId('123456789');
        $this->assertSame($name, (string)$this->account);
    }
}
