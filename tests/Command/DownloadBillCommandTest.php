<?php

namespace AlipayBillBundle\Tests\Command;

use AlipayBillBundle\Command\DownloadBillCommand;
use AlipayBillBundle\Repository\AccountRepository;
use AlipayBillBundle\Repository\BillUrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Service\SmartHttpClient;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\FileNameGenerator\RandomNameGenerator;

class DownloadBillCommandTest extends TestCase
{
    private AccountRepository|MockObject $accountRepository;
    private BillUrlRepository|MockObject $billUrlRepository;
    private LoggerInterface|MockObject $logger;
    private RandomNameGenerator|MockObject $randomNameGenerator;
    private FilesystemOperator|MockObject $filesystem;
    private SmartHttpClient|MockObject $httpClient;
    private EntityManagerInterface|MockObject $entityManager;
    private DownloadBillCommand $command;

    protected function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
        $this->billUrlRepository = $this->createMock(BillUrlRepository::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->randomNameGenerator = $this->createMock(RandomNameGenerator::class);
        $this->filesystem = $this->createMock(FilesystemOperator::class);
        $this->httpClient = $this->createMock(SmartHttpClient::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);

        $this->command = new DownloadBillCommand(
            $this->accountRepository,
            $this->billUrlRepository,
            $this->logger,
            $this->randomNameGenerator,
            $this->filesystem,
            $this->httpClient,
            $this->entityManager
        );
    }

    public function testCommandConfiguration()
    {
        $this->assertInstanceOf(Command::class, $this->command);
        $this->assertEquals('alipay-trade:download-bill', $this->command->getName());
        $this->assertEquals('账单下载', $this->command->getDescription());
    }

    public function testCommandExecution()
    {
        // 由于该命令依赖于外部API，我们简化测试，仅确认可以初始化并执行
        // 对于复杂的业务逻辑，实际项目中应该使用集成测试

        // 设置账户仓库返回空数组
        $this->accountRepository->method('findBy')
            ->with(['valid' => true])
            ->willReturn([]);

        $application = new Application();
        $application->add($this->command);

        $commandTester = new CommandTester($this->command);
        $exitCode = $commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $exitCode);
    }
}
