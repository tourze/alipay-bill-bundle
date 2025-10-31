<?php

namespace AlipayBillBundle\Tests\Command;

use AlipayBillBundle\Command\DownloadBillCommand;
use AlipayBillBundle\Repository\AccountRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\Console\Tester\CommandTester;
use Tourze\PHPUnitSymfonyKernelTest\AbstractCommandTestCase;

/**
 * @internal
 */
#[CoversClass(DownloadBillCommand::class)]
#[RunTestsInSeparateProcesses]
final class DownloadBillCommandTest extends AbstractCommandTestCase
{
    protected function getCommandTester(): CommandTester
    {
        $command = self::getService(DownloadBillCommand::class);

        return new CommandTester($command);
    }

    protected function onSetUp(): void
    {
        $accountRepository = self::getService(AccountRepository::class);

        $accounts = $accountRepository->findBy(['valid' => true]);
        foreach ($accounts as $account) {
            $account->setValid(false);
        }
        self::getEntityManager()->flush();
    }

    public function testCommandExecution(): void
    {
        $commandTester = $this->getCommandTester();

        $exitCode = $commandTester->execute([]);

        $this->assertSame(0, $exitCode);
    }

    public function testCommandHasCorrectName(): void
    {
        $command = self::getService(DownloadBillCommand::class);
        $this->assertSame('alipay-trade:download-bill', $command->getName());
    }
}
