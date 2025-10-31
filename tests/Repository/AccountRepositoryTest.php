<?php

namespace AlipayBillBundle\Tests\Repository;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Repository\AccountRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(AccountRepository::class)]
#[RunTestsInSeparateProcesses]
final class AccountRepositoryTest extends AbstractRepositoryTestCase
{
    private AccountRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(AccountRepository::class);
    }

    /**
     * 创建Account实体的辅助方法，自动添加时间戳
     *
     * @param array<string, mixed> $overrides
     */
    private function createAccountEntity(array $overrides = []): Account
    {
        $data = array_merge($this->getDefaultAccountData(), $overrides);
        $account = new Account();

        $this->setBasicAccountProperties($account, $data);
        $this->setTimestampProperties($account, $data);
        $this->setBlameableProperties($account, $data);
        $this->setIpTraceProperties($account, $data);

        return $account;
    }

    /**
     * 安全地将mixed类型转换为string
     *
     * @param mixed $value
     */
    private function convertToString(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if ($value instanceof \Stringable) {
            return (string) $value;
        }

        if (is_object($value) || is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR);
        }

        // 对于null或其他类型，返回空字符串
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private function getDefaultAccountData(): array
    {
        return [
            'createTime' => new \DateTimeImmutable(),
            'updateTime' => new \DateTimeImmutable(),
            'createdBy' => null,
            'updatedBy' => null,
            'createdFromIp' => null,
            'updatedFromIp' => null,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setBasicAccountProperties(Account $account, array $data): void
    {
        $this->setRequiredAccountFields($account, $data);
        $this->setOptionalAccountFields($account, $data);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setRequiredAccountFields(Account $account, array $data): void
    {
        if (isset($data['name'])) {
            $value = $data['name'];
            $account->setName($this->convertToString($value));
        }
        if (isset($data['appId'])) {
            $value = $data['appId'];
            $account->setAppId($this->convertToString($value));
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setOptionalAccountFields(Account $account, array $data): void
    {
        if (array_key_exists('rsaPrivateKey', $data)) {
            $value = $data['rsaPrivateKey'];
            $account->setRsaPrivateKey(null === $value ? null : $this->convertToString($value));
        }
        if (array_key_exists('rsaPublicKey', $data)) {
            $value = $data['rsaPublicKey'];
            $account->setRsaPublicKey(null === $value ? null : $this->convertToString($value));
        }
        if (array_key_exists('valid', $data)) {
            $value = $data['valid'];
            $account->setValid(null === $value ? null : (bool) $value);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setTimestampProperties(Account $account, array $data): void
    {
        if (isset($data['createTime'])) {
            $value = $data['createTime'];
            $account->setCreateTime($value instanceof \DateTimeImmutable ? $value : null);
        }
        if (isset($data['updateTime'])) {
            $value = $data['updateTime'];
            $account->setUpdateTime($value instanceof \DateTimeImmutable ? $value : null);
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setBlameableProperties(Account $account, array $data): void
    {
        if (array_key_exists('createdBy', $data)) {
            $value = $data['createdBy'];
            $account->setCreatedBy(null === $value ? null : $this->convertToString($value));
        }
        if (array_key_exists('updatedBy', $data)) {
            $value = $data['updatedBy'];
            $account->setUpdatedBy(null === $value ? null : $this->convertToString($value));
        }
    }

    /**
     * @param array<string, mixed> $data
     */
    private function setIpTraceProperties(Account $account, array $data): void
    {
        if (array_key_exists('createdFromIp', $data)) {
            $value = $data['createdFromIp'];
            $account->setCreatedFromIp(null === $value ? null : $this->convertToString($value));
        }
        if (array_key_exists('updatedFromIp', $data)) {
            $value = $data['updatedFromIp'];
            $account->setUpdatedFromIp(null === $value ? null : $this->convertToString($value));
        }
    }

    public function testRepositoryIsCreatedWithCorrectEntityClass(): void
    {
        $this->assertInstanceOf(AccountRepository::class, $this->repository);
    }

    public function testCreateEntityWithDateTimeImmutableWorksCorrectly(): void
    {
        // 清空数据库
        self::getEntityManager()->createQuery('DELETE FROM ' . Account::class)->execute();

        // 测试 EntityFactoryTrait 能正确创建实体而不出现 DateTime 类型错误
        $entity = $this->createAccountEntity(['name' => 'test_datetime', 'appId' => 'test_app', 'createTime' => new \DateTimeImmutable(), 'updateTime' => new \DateTimeImmutable()]);

        // 持久化实体到数据库
        self::getEntityManager()->persist($entity);
        self::getEntityManager()->flush();

        $this->assertInstanceOf(Account::class, $entity);
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getCreateTime());
        $this->assertInstanceOf(\DateTimeImmutable::class, $entity->getUpdateTime());
        $this->assertEquals('test_datetime', $entity->getName());
        $this->assertEquals('test_app', $entity->getAppId());
    }

    // find 相关测试

    // findOneBy 相关测试

    // findAll 相关测试

    // 可空字段测试
    public function testFindByWithNullableFieldsShouldWork(): void
    {
        // 创建有 null 值的测试数据，使用唯一前缀
        $testPrefix = 'nullable_test_' . uniqid();

        $account1 = $this->createAccountEntity([
            'name' => $testPrefix . '_account_with_nulls',
            'appId' => $testPrefix . '_app1',
            'rsaPrivateKey' => null,
            'rsaPublicKey' => null,
            'valid' => null,
        ]);
        self::getEntityManager()->persist($account1);

        $account2 = $this->createAccountEntity([
            'name' => $testPrefix . '_account_with_values',
            'appId' => $testPrefix . '_app2',
            'rsaPrivateKey' => 'private_key',
            'rsaPublicKey' => 'public_key',
            'valid' => true,
        ]);
        self::getEntityManager()->persist($account2);

        self::getEntityManager()->flush();

        // 测试查找 null 值
        $results = $this->repository->findBy(['rsaPrivateKey' => null]);
        $testResults = array_filter($results, fn (Account $account) => str_starts_with($account->getName(), $testPrefix)
        );
        $this->assertCount(1, $testResults);
        $testResult = array_values($testResults)[0];
        $this->assertSame($testPrefix . '_account_with_nulls', $testResult->getName());

        // 测试查找非 null 值，使用更具体的查询条件
        $results = $this->repository->findBy(['valid' => true]);
        $testResults = array_filter($results, fn (Account $account) => str_starts_with($account->getName(), $testPrefix) && true === $account->isValid()
        );
        $this->assertCount(1, $testResults);
        $testResult = array_values($testResults)[0];
        $this->assertSame($testPrefix . '_account_with_values', $testResult->getName());
    }

    // 关联查询和复杂查询测试
    public function testFindByWithMultipleCriteriaShouldWork(): void
    {
        // 创建测试数据
        $account1 = $this->createAccountEntity([
            'name' => 'valid_account1',
            'appId' => 'app1',
            'valid' => true,
        ]);
        self::getEntityManager()->persist($account1);

        $account2 = $this->createAccountEntity([
            'name' => 'valid_account2',
            'appId' => 'app2',
            'valid' => true,
        ]);
        self::getEntityManager()->persist($account2);

        $account3 = $this->createAccountEntity([
            'name' => 'invalid_account',
            'appId' => 'app3',
            'valid' => false,
        ]);
        self::getEntityManager()->persist($account3);

        self::getEntityManager()->flush();

        // 测试多条件查询
        $results = $this->repository->findBy([
            'appId' => 'app1',
            'valid' => true,
        ]);

        $this->assertCount(1, $results);
        $this->assertSame('valid_account1', $results[0]->getName());
        $this->assertSame('app1', $results[0]->getAppId());
        $this->assertTrue($results[0]->isValid());
    }

    public function testFindByWithUniqueConstraintFieldsShouldWork(): void
    {
        // 创建测试数据
        $account = $this->createAccountEntity([
            'name' => 'unique_name_test',
            'appId' => 'unique_app_test',
        ]);
        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        // 测试通过唯一字段查找
        $resultByName = $this->repository->findBy(['name' => 'unique_name_test']);
        $this->assertCount(1, $resultByName);

        $resultByAppId = $this->repository->findBy(['appId' => 'unique_app_test']);
        $this->assertCount(1, $resultByAppId);

        // 验证是同一个实体
        $this->assertSame($resultByName[0]->getId(), $resultByAppId[0]->getId());
    }

    // findOneBy 排序测试 (按照 PHPStan 规则要求的命名模式)

    // save 和 remove 方法测试
    public function testSaveShouldPersistEntity(): void
    {
        $account = new Account();
        $account->setName('test_save_account');
        $account->setAppId('test_save_app');

        $this->repository->save($account);

        // 验证实体已保存
        $found = $this->repository->findOneBy(['name' => 'test_save_account']);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertSame('test_save_account', $found->getName());
        $this->assertSame('test_save_app', $found->getAppId());
    }

    public function testSaveWithoutFlushShouldNotCommitToDatabase(): void
    {
        $account = new Account();
        $account->setName('test_no_flush_account');
        $account->setAppId('test_no_flush_app');

        $this->repository->save($account, false);

        // 清理实体管理器缓存
        self::getEntityManager()->clear();

        // 验证实体未提交到数据库
        $found = $this->repository->findOneBy(['name' => 'test_no_flush_account']);
        $this->assertNull($found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        // 创建并保存实体
        $account = $this->createAccountEntity(['name' => 'test_remove_account', 'appId' => 'test_remove_app']);
        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();
        $accountId = $account->getId();

        // 删除实体
        $this->repository->remove($account);

        // 验证实体已删除
        $found = $this->repository->find($accountId);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotCommitDeletion(): void
    {
        // 创建并保存实体
        $account = $this->createAccountEntity(['name' => 'test_no_flush_remove', 'appId' => 'test_no_flush_app']);
        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();
        $accountId = $account->getId();

        // 删除实体但不提交
        $this->repository->remove($account, false);

        // 清理实体管理器缓存
        self::getEntityManager()->clear();

        // 验证实体仍存在于数据库
        $found = $this->repository->find($accountId);
        $this->assertInstanceOf(Account::class, $found);
        $this->assertSame('test_no_flush_remove', $found->getName());
    }

    // IS NULL 查询测试
    public function testCountWithNullValuesShouldWork(): void
    {
        // 创建有 null 值的测试数据，使用唯一前缀
        $testPrefix = 'null_test_' . uniqid();
        $account1 = $this->createAccountEntity([
            'name' => $testPrefix . '_null_private_key1',
            'appId' => $testPrefix . '_app1',
            'rsaPrivateKey' => null,
            'rsaPublicKey' => 'public1',
        ]);
        self::getEntityManager()->persist($account1);

        $account2 = $this->createAccountEntity([
            'name' => $testPrefix . '_null_private_key2',
            'appId' => $testPrefix . '_app2',
            'rsaPrivateKey' => null,
            'rsaPublicKey' => null,
        ]);
        self::getEntityManager()->persist($account2);

        $account3 = $this->createAccountEntity([
            'name' => $testPrefix . '_has_private_key',
            'appId' => $testPrefix . '_app3',
            'rsaPrivateKey' => 'private_key',
            'rsaPublicKey' => 'public_key',
        ]);
        self::getEntityManager()->persist($account3);

        self::getEntityManager()->flush();

        // 测试查找我们创建的 null 值记录
        $allNullPrivateKey = $this->repository->findBy(['rsaPrivateKey' => null]);
        $testNullPrivateKey = array_filter($allNullPrivateKey, fn (Account $account) => str_starts_with($account->getName(), $testPrefix)
        );
        $this->assertCount(2, $testNullPrivateKey);

        $allNullPublicKey = $this->repository->findBy(['rsaPublicKey' => null]);
        $testNullPublicKey = array_filter($allNullPublicKey, fn (Account $account) => str_starts_with($account->getName(), $testPrefix)
        );
        $this->assertCount(1, $testNullPublicKey);

        // 测试验证具有 private key 的记录
        $account4 = $this->createAccountEntity([
            'name' => $testPrefix . '_another_has_private_key',
            'appId' => $testPrefix . '_app4',
            'rsaPrivateKey' => 'another_private_key',
        ]);
        self::getEntityManager()->persist($account4);
        self::getEntityManager()->flush();

        // 验证我们创建的具有 private key 的记录
        $allRecords = $this->repository->findAll();
        $testRecords = array_filter($allRecords, fn (Account $account) => str_starts_with($account->getName(), $testPrefix)
        );
        $hasPrivateKeyRecords = array_filter($testRecords, fn (Account $account) => null !== $account->getRsaPrivateKey()
        );
        $this->assertCount(2, $hasPrivateKeyRecords);
    }

    // 按照 PHPStan 规则要求的命名模式添加 count IS NULL 查询测试

    public function testFindByWithIsNullQueryShouldWork(): void
    {
        // 清空数据库以确保测试数据干净
        self::getEntityManager()->createQuery('DELETE FROM ' . Account::class)->execute();

        // 创建测试数据，使用唯一前缀避免冲突
        $testPrefix = 'is_null_test_' . uniqid();

        $account1 = $this->createAccountEntity([
            'name' => $testPrefix . '_has_all_fields',
            'appId' => $testPrefix . '_app1',
            'rsaPrivateKey' => 'private1',
            'rsaPublicKey' => 'public1',
            'valid' => true,
        ]);
        self::getEntityManager()->persist($account1);

        $account2 = $this->createAccountEntity([
            'name' => $testPrefix . '_missing_keys',
            'appId' => $testPrefix . '_app2',
            'rsaPrivateKey' => null,
            'rsaPublicKey' => null,
            'valid' => true, // 修改为 true，因为 valid 字段不能为 null
        ]);
        self::getEntityManager()->persist($account2);

        $account3 = $this->createAccountEntity([
            'name' => $testPrefix . '_partial_keys',
            'appId' => $testPrefix . '_app3',
            'rsaPrivateKey' => 'private3',
            'rsaPublicKey' => null,
            'valid' => false,
        ]);
        self::getEntityManager()->persist($account3);

        self::getEntityManager()->flush();

        // 测试查找 rsaPrivateKey 为 null 的记录
        $results = $this->repository->findBy(['rsaPrivateKey' => null]);
        $testResults = array_filter($results, fn (Account $account) => str_starts_with($account->getName(), $testPrefix));
        $this->assertCount(1, $testResults);
        $this->assertSame($testPrefix . '_missing_keys', array_values($testResults)[0]->getName());

        // 测试查找 rsaPublicKey 为 null 的记录
        $results = $this->repository->findBy(['rsaPublicKey' => null]);
        $testResults = array_filter($results, fn (Account $account) => str_starts_with($account->getName(), $testPrefix));
        $this->assertCount(2, $testResults);
        $names = array_map(fn (Account $account) => $account->getName(), $testResults);
        $this->assertContains($testPrefix . '_missing_keys', $names);
        $this->assertContains($testPrefix . '_partial_keys', $names);

        // 测试查找 valid 为 true 的记录
        $results = $this->repository->findBy(['valid' => true]);
        $testResults = array_filter($results, fn (Account $account) => str_starts_with($account->getName(), $testPrefix));
        $this->assertCount(2, $testResults);
        $names = array_map(fn (Account $account) => $account->getName(), $testResults);
        $this->assertContains($testPrefix . '_has_all_fields', $names);
        $this->assertContains($testPrefix . '_missing_keys', $names);

        // 测试查找 valid 为 false 的记录
        $results = $this->repository->findBy(['valid' => false]);
        $testResults = array_filter($results, fn (Account $account) => str_starts_with($account->getName(), $testPrefix));
        $this->assertCount(1, $testResults);
        $this->assertSame($testPrefix . '_partial_keys', array_values($testResults)[0]->getName());
    }

    // 按照 PHPStan 规则要求的命名模式添加 findBy IS NULL 查询测试

    // 按照 PHPStan 规则要求的命名模式添加 IS NULL 查询测试

    /**
     * @return ServiceEntityRepository<Account>
     */
    protected function getRepository(): ServiceEntityRepository
    {
        return $this->repository;
    }

    protected function createNewEntity(): object
    {
        $account = new Account();
        $account->setName('test_account_' . uniqid());
        $account->setAppId('test_app_' . uniqid());
        $account->setValid(true);
        $account->setCreateTime(new \DateTimeImmutable());
        $account->setUpdateTime(new \DateTimeImmutable());

        return $account;
    }
}
