<?php

namespace AlipayBillBundle\Tests\Entity;

use AlipayBillBundle\Entity\Account;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Account::class)]
final class AccountTest extends AbstractEntityTestCase
{
    /**
     * 创建被测实体的一个实例.
     */
    protected function createEntity(): object
    {
        return new Account();
    }

    /**
     * 提供属性及其样本值的 Data Provider.
     *
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        yield 'name' => ['name', '测试账号'];
        yield 'appId' => ['appId', '2022123456789'];
        yield 'rsaPrivateKey' => ['rsaPrivateKey', 'MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC7VJTUt9Us8cKj'];
        yield 'rsaPublicKey' => ['rsaPublicKey', 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAut9evKRuHJ/2QNfDlLwv'];
        yield 'valid' => ['valid', true];
        yield 'validFalse' => ['valid', false];
        yield 'createdBy' => ['createdBy', 'admin'];
        yield 'updatedBy' => ['updatedBy', 'admin'];
        yield 'createdFromIp' => ['createdFromIp', '127.0.0.1'];
        yield 'updatedFromIp' => ['updatedFromIp', '127.0.0.1'];
        yield 'createTime' => ['createTime', new \DateTimeImmutable()];
        yield 'updateTime' => ['updateTime', new \DateTimeImmutable()];
    }

    public function testToString(): void
    {
        $account = $this->createEntity();
        $this->assertInstanceOf(Account::class, $account);

        $this->assertSame('', (string) $account);

        $name = '测试账号';
        $account->setName($name);

        // 没有ID时应返回空字符串
        $this->assertSame('', (string) $account);

        // 设置ID后应返回名称
        $account->setId('123456789');
        $this->assertSame($name, (string) $account);
    }
}
