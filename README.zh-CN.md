# AlipayBillBundle

[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/alipay-bill-bundle)](https://packagist.org/packages/tourze/alipay-bill-bundle)
[![License](https://img.shields.io/packagist/l/tourze/alipay-bill-bundle)](https://packagist.org/packages/tourze/alipay-bill-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/monorepo/ci.yml)](https://github.com/tourze/monorepo/actions)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/monorepo)](https://codecov.io/gh/tourze/monorepo)

[English](README.md) | [中文](README.zh-CN.md)

用于管理支付宝账单下载和账户管理的 Symfony Bundle。

## 目录

- [功能特性](#功能特性)
- [依赖要求](#依赖要求)
- [安装](#安装)
- [配置](#配置)
  - [1. 注册 Bundle](#1-注册-bundle)
  - [2. 数据库架构](#2-数据库架构)
- [使用方法](#使用方法)
  - [管理支付宝账户](#管理支付宝账户)
  - [下载账单](#下载账单)
  - [账单类型](#账单类型)
  - [实体](#实体)
  - [仓储](#仓储)
- [高级用法](#高级用法)
  - [自定义账单处理](#自定义账单处理)
  - [与消息队列集成](#与消息队列集成)
  - [自定义验证规则](#自定义验证规则)
- [安全注意事项](#安全注意事项)
- [许可证](#许可证)

## 功能特性

- 支付宝账户管理，支持安全的凭证存储
- 通过控制台命令自动下载账单
- 支持多种账单类型（trade、signcustomer、merchant_act、settlementMerge）
- 集成 EasyAdmin，提供账户和账单 URL 管理界面
- 支持定时任务自动下载账单
- 所有操作均有 IP 跟踪和审计记录

## 依赖要求

此 Bundle 需要：
- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本
- 支付宝 OpenAPI SDK 3.0 或更高版本

内部依赖：
- tourze/bundle-dependency：用于 Bundle 依赖管理
- tourze/doctrine-snowflake-bundle：用于 ID 生成
- tourze/doctrine-timestamp-bundle：用于时间戳跟踪
- tourze/doctrine-user-bundle：用于用户跟踪
- tourze/doctrine-ip-bundle：用于 IP 跟踪
- tourze/easy-admin-menu-bundle：用于 EasyAdmin 菜单集成

## 安装

```bash
composer require tourze/alipay-bill-bundle
```

## 配置

### 1. 注册 Bundle

如果没有使用 Symfony Flex，需要在 `config/bundles.php` 中注册 Bundle：

```php
return [
    // ...
    AlipayBillBundle\AlipayBillBundle::class => ['all' => true],
];
```

### 2. 数据库架构

更新数据库架构以创建所需的表：

```bash
php bin/console doctrine:schema:update --force
```

## 使用方法

### 管理支付宝账户

Bundle 提供了 EasyAdmin CRUD 控制器来管理支付宝账户：

1. 访问你的 EasyAdmin 仪表板
2. 点击"支付宝账户"菜单
3. 添加新账户，需要提供：
    - 名称：账户的唯一标识符
    - App ID：你的支付宝应用 ID
    - RSA 私钥：你的应用私钥
    - RSA 公钥：你的应用公钥
    - 有效：启用/禁用账户

### 下载账单

#### 手动下载

使用控制台命令下载账单：

```bash
php bin/console alipay-trade:download-bill
```

该命令将：
- 获取所有有效的支付宝账户
- 下载前一天的账单
- 在数据库中存储账单 URL
- 将账单文件保存到文件系统

#### 定时下载

命令配置了 cron 表达式，自动运行时间：
- 每天上午 9:00
- 每天上午 10:00

### 账单类型

Bundle 支持以下账单类型：

- `trade`：商户基于支付宝交易收单的业务账单
- `signcustomer`：基于商户支付宝余额收入及支出等资金变动的账务账单
- `merchant_act`：营销活动账单，包含营销活动的发放、核销记录
- `settlementMerge`：每日结算到卡的资金对应的明细

### 实体

#### Account 实体

管理支付宝账户凭证：

```php
use AlipayBillBundle\Entity\Account;

$account = new Account();
$account->setName('我的支付宝账户');
$account->setAppId('your_app_id');
$account->setRsaPrivateKey('your_private_key');
$account->setRsaPublicKey('your_public_key');
$account->setValid(true);
```

#### BillUrl 实体

存储下载的账单信息：

```php
use AlipayBillBundle\Entity\BillUrl;

// 账单由下载命令自动创建
// 你可以使用仓储来查询它们
$bills = $billUrlRepository->findBy(['account' => $account]);
```

### 仓储

Bundle 提供了带有额外查询方法的仓储：

```php
use AlipayBillBundle\Repository\AccountRepository;
use AlipayBillBundle\Repository\BillUrlRepository;

// 获取所有有效账户
$validAccounts = $accountRepository->findBy(['valid' => true]);

// 按日期范围查找账单
$bills = $billUrlRepository->createQueryBuilder('b')
    ->where('b.billDate BETWEEN :start AND :end')
    ->setParameter('start', $startDate)
    ->setParameter('end', $endDate)
    ->getQuery()
    ->getResult();
```

## 高级用法

### 自定义账单处理

你可以通过扩展仓储或创建服务来创建自定义账单处理器：

```php
use AlipayBillBundle\Repository\BillUrlRepository;
use Doctrine\ORM\EntityManagerInterface;

class CustomBillProcessor
{
    public function __construct(
        private BillUrlRepository $billUrlRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function processBillsForAccount(Account $account): void
    {
        $bills = $this->billUrlRepository->findBy([
            'account' => $account,
            'localFile' => null // 未处理的账单
        ]);

        foreach ($bills as $bill) {
            // 在这里添加自定义处理逻辑
            $this->processIndividualBill($bill);
        }
    }
}
```

### 与消息队列集成

对于高并发场景，你可以与 Symfony Messenger 集成：

```php
use Symfony\Component\Messenger\MessageBusInterface;

class AsyncBillDownloader
{
    public function __construct(private MessageBusInterface $messageBus) {}

    public function scheduleDownload(Account $account, \DateTimeInterface $date): void
    {
        $this->messageBus->dispatch(new DownloadBillMessage($account->getId(), $date));
    }
}
```

### 自定义验证规则

为你的特定需求添加自定义验证：

```php
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback]
public function validateAppId(ExecutionContextInterface $context): void
{
    if (!preg_match('/^[0-9]{15,16}$/', $this->appId)) {
        $context->buildViolation('App ID 必须是 15-16 位数字')
            ->atPath('appId')
            ->addViolation();
    }
}
```

## 安全注意事项

- RSA 密钥存储在数据库中 - 请确保数据库安全
- 尽可能使用环境变量存储敏感配置
- 通过 IP 跟踪定期审计账户访问
- 为 EasyAdmin 界面实施适当的访问控制

## 许可证

MIT