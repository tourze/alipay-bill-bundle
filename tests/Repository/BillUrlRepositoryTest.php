<?php

namespace AlipayBillBundle\Tests\Repository;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Enum\BillType;
use AlipayBillBundle\Repository\BillUrlRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * @internal
 */
#[CoversClass(BillUrlRepository::class)]
#[RunTestsInSeparateProcesses]
final class BillUrlRepositoryTest extends AbstractRepositoryTestCase
{
    private BillUrlRepository $repository;

    protected function onSetUp(): void
    {
        $this->repository = self::getService(BillUrlRepository::class);
    }

    public function testRepositoryIsCreatedWithCorrectEntityClass(): void
    {
        $this->assertInstanceOf(BillUrlRepository::class, $this->repository);
    }

    public function testCountByAssociationAccountShouldReturnCorrectNumber(): void
    {
        $account1 = $this->createAccountEntity(['name' => 'account1', 'appId' => 'app1']);
        $account2 = $this->createAccountEntity(['name' => 'account2', 'appId' => 'app2']);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $billUrl1 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/bill1',
        ]);
        $billUrl2 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::signcustomer,
            'date' => new \DateTimeImmutable('2023-01-02'),
            'downloadUrl' => 'https://example.com/bill2',
        ]);
        $billUrl3 = $this->createBillUrlEntity([
            'account' => $account2,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-03'),
            'downloadUrl' => 'https://example.com/bill3',
        ]);

        self::getEntityManager()->persist($billUrl1);
        self::getEntityManager()->persist($billUrl2);
        self::getEntityManager()->persist($billUrl3);
        self::getEntityManager()->flush();

        $count = $this->repository->count(['account' => $account1]);
        $this->assertSame(2, $count);
    }

    public function testFindOneByAssociationAccountShouldReturnMatchingEntity(): void
    {
        $account1 = $this->createAccountEntity(['name' => 'account1', 'appId' => 'app1']);
        $account2 = $this->createAccountEntity(['name' => 'account2', 'appId' => 'app2']);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $billUrl1 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/bill1',
        ]);
        $billUrl2 = $this->createBillUrlEntity([
            'account' => $account2,
            'type' => BillType::signcustomer,
            'date' => new \DateTimeImmutable('2023-01-02'),
            'downloadUrl' => 'https://example.com/bill2',
        ]);

        self::getEntityManager()->persist($billUrl1);
        self::getEntityManager()->persist($billUrl2);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy(['account' => $account1]);
        $this->assertInstanceOf(BillUrl::class, $result);
        $this->assertSame($account1->getId(), $result->getAccount()->getId());
        $this->assertSame(BillType::trade, $result->getType());
    }

    public function testFindByWithAccountAssociationShouldWork(): void
    {
        $account1 = $this->createAccountEntity(['name' => 'account1', 'appId' => 'app1']);
        $account2 = $this->createAccountEntity(['name' => 'account2', 'appId' => 'app2']);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $billUrl1 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/bill1',
        ]);
        $billUrl2 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::signcustomer,
            'date' => new \DateTimeImmutable('2023-01-02'),
            'downloadUrl' => 'https://example.com/bill2',
        ]);
        $billUrl3 = $this->createBillUrlEntity([
            'account' => $account2,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-03'),
            'downloadUrl' => 'https://example.com/bill3',
        ]);

        self::getEntityManager()->persist($billUrl1);
        self::getEntityManager()->persist($billUrl2);
        self::getEntityManager()->persist($billUrl3);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['account' => $account1]);
        $this->assertCount(2, $results);

        foreach ($results as $billUrl) {
            $this->assertInstanceOf(BillUrl::class, $billUrl);
            $this->assertSame($account1->getId(), $billUrl->getAccount()->getId());
        }
    }

    public function testFindByWithMultipleCriteriaShouldWork(): void
    {
        $account1 = $this->createAccountEntity(['name' => 'account1', 'appId' => 'app1']);
        $account2 = $this->createAccountEntity(['name' => 'account2', 'appId' => 'app2']);

        self::getEntityManager()->persist($account1);
        self::getEntityManager()->persist($account2);
        self::getEntityManager()->flush();

        $billUrl1 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/bill1',
        ]);
        $billUrl2 = $this->createBillUrlEntity([
            'account' => $account1,
            'type' => BillType::signcustomer,
            'date' => new \DateTimeImmutable('2023-01-02'),
            'downloadUrl' => 'https://example.com/bill2',
        ]);
        $billUrl3 = $this->createBillUrlEntity([
            'account' => $account2,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-03'),
            'downloadUrl' => 'https://example.com/bill3',
        ]);

        self::getEntityManager()->persist($billUrl1);
        self::getEntityManager()->persist($billUrl2);
        self::getEntityManager()->persist($billUrl3);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy([
            'account' => $account1,
            'type' => BillType::trade,
        ]);

        $this->assertCount(1, $results);
        $this->assertSame($account1->getId(), $results[0]->getAccount()->getId());
        $this->assertSame(BillType::trade, $results[0]->getType());
    }

    public function testFindByWithNullableFieldsShouldWork(): void
    {
        self::getEntityManager()->createQuery('DELETE FROM ' . BillUrl::class)->execute();

        $account = $this->createAccountEntity(['name' => 'test_account', 'appId' => 'test_app']);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl1 = $this->createBillUrlEntity([
            'account' => $account,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/bill1',
            'localFile' => null,
        ]);
        $billUrl2 = $this->createBillUrlEntity([
            'account' => $account,
            'type' => BillType::signcustomer,
            'date' => new \DateTimeImmutable('2023-01-02'),
            'downloadUrl' => 'https://example.com/bill2',
            'localFile' => '/path/to/local/file',
        ]);

        self::getEntityManager()->persist($billUrl1);
        self::getEntityManager()->persist($billUrl2);
        self::getEntityManager()->flush();

        $results = $this->repository->findBy(['localFile' => null]);
        $this->assertCount(1, $results);
        $this->assertSame(BillType::trade, $results[0]->getType());

        $results = $this->repository->findBy(['localFile' => '/path/to/local/file']);
        $this->assertCount(1, $results);
        $this->assertSame(BillType::signcustomer, $results[0]->getType());
    }

    public function testSaveShouldPersistEntity(): void
    {
        $account = $this->createAccountEntity(['name' => 'test_account', 'appId' => 'test_app']);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl = new BillUrl();
        $billUrl->setAccount($account);
        $billUrl->setType(BillType::trade);
        $billUrl->setDate(new \DateTimeImmutable('2023-01-01'));
        $billUrl->setDownloadUrl('https://example.com/test-save-bill');

        $this->repository->save($billUrl);

        $found = $this->repository->findOneBy(['downloadUrl' => 'https://example.com/test-save-bill']);
        $this->assertInstanceOf(BillUrl::class, $found);
        $this->assertSame('https://example.com/test-save-bill', $found->getDownloadUrl());
        $this->assertSame(BillType::trade, $found->getType());
    }

    public function testSaveWithoutFlushShouldNotCommitToDatabase(): void
    {
        $account = $this->createAccountEntity(['name' => 'test_account', 'appId' => 'test_app']);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl = new BillUrl();
        $billUrl->setAccount($account);
        $billUrl->setType(BillType::trade);
        $billUrl->setDate(new \DateTimeImmutable('2023-01-01'));
        $billUrl->setDownloadUrl('https://example.com/test-no-flush-bill');

        $this->repository->save($billUrl, false);

        self::getEntityManager()->clear();

        $found = $this->repository->findOneBy(['downloadUrl' => 'https://example.com/test-no-flush-bill']);
        $this->assertNull($found);
    }

    public function testRemoveShouldDeleteEntity(): void
    {
        $account = $this->createAccountEntity(['name' => 'test_account', 'appId' => 'test_app']);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl = $this->createBillUrlEntity([
            'account' => $account,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/test-remove-bill',
        ]);

        self::getEntityManager()->persist($billUrl);
        self::getEntityManager()->flush();

        $billUrlId = $billUrl->getId();

        $this->repository->remove($billUrl);

        $found = $this->repository->find($billUrlId);
        $this->assertNull($found);
    }

    public function testRemoveWithoutFlushShouldNotCommitDeletion(): void
    {
        $account = $this->createAccountEntity(['name' => 'test_account', 'appId' => 'test_app']);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl = $this->createBillUrlEntity([
            'account' => $account,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/test-no-flush-remove',
        ]);

        self::getEntityManager()->persist($billUrl);
        self::getEntityManager()->flush();

        $billUrlId = $billUrl->getId();

        $this->repository->remove($billUrl, false);

        self::getEntityManager()->clear();

        $found = $this->repository->find($billUrlId);
        $this->assertInstanceOf(BillUrl::class, $found);
        $this->assertSame('https://example.com/test-no-flush-remove', $found->getDownloadUrl());
    }

    public function testFindByWithUniqueConstraintFieldsShouldWork(): void
    {
        $account = $this->createAccountEntity(['name' => 'test_account', 'appId' => 'test_app']);

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl = $this->createBillUrlEntity([
            'account' => $account,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
            'downloadUrl' => 'https://example.com/unique-bill',
        ]);

        self::getEntityManager()->persist($billUrl);
        self::getEntityManager()->flush();

        $result = $this->repository->findOneBy([
            'account' => $account,
            'type' => BillType::trade,
            'date' => new \DateTimeImmutable('2023-01-01'),
        ]);

        $this->assertInstanceOf(BillUrl::class, $result);
        $this->assertSame('https://example.com/unique-bill', $result->getDownloadUrl());
    }

    /**
     * @return ServiceEntityRepository<BillUrl>
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

        self::getEntityManager()->persist($account);
        self::getEntityManager()->flush();

        $billUrl = new BillUrl();
        $billUrl->setAccount($account);
        $billUrl->setType(BillType::trade);
        $billUrl->setDate(new \DateTimeImmutable('2023-01-01'));
        $billUrl->setDownloadUrl('https://example.com/bill_' . uniqid());
        $billUrl->setCreateTime(new \DateTimeImmutable());
        $billUrl->setUpdateTime(new \DateTimeImmutable());

        return $billUrl;
    }

    // 简化的辅助方法
    /**
     * @param array<string, mixed> $overrides
     */
    private function createAccountEntity(array $overrides = []): Account
    {
        $account = new Account();
        $account->setName($overrides['name'] ?? 'test_account');
        $account->setAppId($overrides['appId'] ?? 'test_app_id');
        $account->setValid($overrides['valid'] ?? true);
        $account->setCreateTime(new \DateTimeImmutable());
        $account->setUpdateTime(new \DateTimeImmutable());

        return $account;
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createBillUrlEntity(array $overrides = []): BillUrl
    {
        $billUrl = new BillUrl();
        $billUrl->setAccount($overrides['account'] ?? $this->createAccountEntity());
        $billUrl->setType($overrides['type'] ?? BillType::trade);
        $billUrl->setDate($overrides['date'] ?? new \DateTimeImmutable('2023-01-01'));
        $billUrl->setDownloadUrl($overrides['downloadUrl'] ?? 'https://example.com/bill.csv');
        $billUrl->setLocalFile($overrides['localFile'] ?? null);
        $billUrl->setCreateTime(new \DateTimeImmutable());
        $billUrl->setUpdateTime(new \DateTimeImmutable());

        return $billUrl;
    }
}
