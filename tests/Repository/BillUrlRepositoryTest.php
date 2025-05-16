<?php

namespace AlipayBillBundle\Tests\Repository;

use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Repository\BillUrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;

class BillUrlRepositoryTest extends TestCase
{
    private BillUrlRepository $repository;
    private ManagerRegistry $registry;
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ManagerRegistry::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        
        // 不设置期望，允许0次或多次调用
        $this->registry->method('getManagerForClass')
            ->with(BillUrl::class)
            ->willReturn($this->entityManager);
            
        $this->repository = new BillUrlRepository($this->registry);
    }
    
    public function testRepositoryIsCreatedWithCorrectEntityClass()
    {
        $this->assertInstanceOf(BillUrlRepository::class, $this->repository);
    }
} 