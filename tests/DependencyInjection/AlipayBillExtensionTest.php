<?php

namespace AlipayBillBundle\Tests\DependencyInjection;

use AlipayBillBundle\Command\DownloadBillCommand;
use AlipayBillBundle\DependencyInjection\AlipayBillExtension;
use AlipayBillBundle\Repository\AccountRepository;
use AlipayBillBundle\Repository\BillUrlRepository;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class AlipayBillExtensionTest extends TestCase
{
    private AlipayBillExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new AlipayBillExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoad()
    {
        $this->extension->load([], $this->container);
        
        // 验证服务自动发现注册
        $this->assertTrue($this->container->has(DownloadBillCommand::class));
        $this->assertTrue($this->container->has(AccountRepository::class));
        $this->assertTrue($this->container->has(BillUrlRepository::class));
    }
    
    public function testServicesAreConfigured()
    {
        $this->extension->load([], $this->container);
        
        // 检查服务是否被正确配置
        $commandDefinition = $this->container->getDefinition(DownloadBillCommand::class);
        $this->assertTrue($commandDefinition->isAutowired());
        $this->assertTrue($commandDefinition->isAutoconfigured());
        
        $accountRepoDefinition = $this->container->getDefinition(AccountRepository::class);
        $this->assertTrue($accountRepoDefinition->isAutowired());
        $this->assertTrue($accountRepoDefinition->isAutoconfigured());
        
        $billUrlRepoDefinition = $this->container->getDefinition(BillUrlRepository::class);
        $this->assertTrue($billUrlRepoDefinition->isAutowired());
        $this->assertTrue($billUrlRepoDefinition->isAutoconfigured());
    }
} 