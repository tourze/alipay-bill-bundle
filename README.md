# AlipayBillBundle

[![PHP Version Require](https://img.shields.io/packagist/php-v/tourze/alipay-bill-bundle)](https://packagist.org/packages/tourze/alipay-bill-bundle)
[![License](https://img.shields.io/packagist/l/tourze/alipay-bill-bundle)](https://packagist.org/packages/tourze/alipay-bill-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/monorepo/ci.yml)](https://github.com/tourze/monorepo/actions)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/monorepo)](https://codecov.io/gh/tourze/monorepo)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for managing Alipay bill downloads and account management.

## Table of Contents

- [Features](#features)
- [Dependencies](#dependencies)
- [Installation](#installation)
- [Configuration](#configuration)
  - [1. Register the Bundle](#1-register-the-bundle)
  - [2. Database Schema](#2-database-schema)
- [Usage](#usage)
  - [Managing Alipay Accounts](#managing-alipay-accounts)
  - [Downloading Bills](#downloading-bills)
  - [Bill Types](#bill-types)
  - [Entities](#entities)
  - [Repositories](#repositories)
- [Advanced Usage](#advanced-usage)
  - [Custom Bill Processing](#custom-bill-processing)
  - [Integration with Message Queues](#integration-with-message-queues)
  - [Custom Validation Rules](#custom-validation-rules)
- [Security Considerations](#security-considerations)
- [License](#license)

## Features

- Alipay account management with secure credential storage
- Automated bill downloading via console commands
- Multiple bill type support (trade, signcustomer, merchant_act, settlementMerge)
- EasyAdmin integration for account and bill URL management
- Scheduled bill downloads with cron job support
- IP tracking and audit trail for all operations

## Dependencies

This bundle requires:
- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher
- Alipay OpenAPI SDK 3.0 or higher

Internal dependencies:
- tourze/bundle-dependency: For bundle dependency management
- tourze/doctrine-snowflake-bundle: For ID generation
- tourze/doctrine-timestamp-bundle: For timestamp tracking
- tourze/doctrine-user-bundle: For user tracking
- tourze/doctrine-ip-bundle: For IP tracking
- tourze/easy-admin-menu-bundle: For EasyAdmin menu integration

## Installation

```bash
composer require tourze/alipay-bill-bundle
```

## Configuration

### 1. Register the Bundle

If not using Symfony Flex, register the bundle in your `config/bundles.php`:

```php
return [
    // ...
    AlipayBillBundle\AlipayBillBundle::class => ['all' => true],
];
```

### 2. Database Schema

Update your database schema to create the required tables:

```bash
php bin/console doctrine:schema:update --force
```

## Usage

### Managing Alipay Accounts

The bundle provides EasyAdmin CRUD controllers for managing Alipay accounts:

1. Navigate to your EasyAdmin dashboard
2. Access "Alipay Accounts" menu
3. Add new accounts with:
    - Name: A unique identifier for the account
    - App ID: Your Alipay application ID
    - RSA Private Key: Your application's private key
    - RSA Public Key: Your application's public key
    - Valid: Enable/disable the account

### Downloading Bills

#### Manual Download

Use the console command to download bills:

```bash
php bin/console alipay-trade:download-bill
```

This command will:
- Fetch all valid Alipay accounts
- Download bills for the previous day
- Store bill URLs in the database
- Save bill files to the filesystem

#### Scheduled Downloads

The command is configured with cron expressions to run automatically:
- Daily at 9:00 AM
- Daily at 10:00 AM

### Bill Types

The bundle supports the following bill types:

- `trade`: Merchant transaction bills based on Alipay payment collection
- `signcustomer`: Account bills based on Alipay balance income and expenses
- `merchant_act`: Marketing activity bills including distribution and verification records
- `settlementMerge`: Daily settlement details for funds transferred to cards

### Entities

#### Account Entity

Manages Alipay account credentials:

```php
use AlipayBillBundle\Entity\Account;

$account = new Account();
$account->setName('My Alipay Account');
$account->setAppId('your_app_id');
$account->setRsaPrivateKey('your_private_key');
$account->setRsaPublicKey('your_public_key');
$account->setValid(true);
```

#### BillUrl Entity

Stores downloaded bill information:

```php
use AlipayBillBundle\Entity\BillUrl;

// Bills are automatically created by the download command
// You can query them using the repository
$bills = $billUrlRepository->findBy(['account' => $account]);
```

### Repositories

The bundle provides repositories with additional query methods:

```php
use AlipayBillBundle\Repository\AccountRepository;
use AlipayBillBundle\Repository\BillUrlRepository;

// Get all valid accounts
$validAccounts = $accountRepository->findBy(['valid' => true]);

// Find bills by date range
$bills = $billUrlRepository->createQueryBuilder('b')
    ->where('b.billDate BETWEEN :start AND :end')
    ->setParameter('start', $startDate)
    ->setParameter('end', $endDate)
    ->getQuery()
    ->getResult();
```

## Advanced Usage

### Custom Bill Processing

You can create custom bill processors by extending the repository or creating services:

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
            'localFile' => null // Unprocessed bills
        ]);

        foreach ($bills as $bill) {
            // Custom processing logic here
            $this->processIndividualBill($bill);
        }
    }
}
```

### Integration with Message Queues

For high-volume scenarios, you can integrate with Symfony Messenger:

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

### Custom Validation Rules

Add custom validation for your specific requirements:

```php
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[Assert\Callback]
public function validateAppId(ExecutionContextInterface $context): void
{
    if (!preg_match('/^[0-9]{15,16}$/', $this->appId)) {
        $context->buildViolation('App ID must be 15-16 digits')
            ->atPath('appId')
            ->addViolation();
    }
}
```

## Security Considerations

- RSA keys are stored in the database - ensure proper database security
- Use environment variables for sensitive configuration when possible
- Regular audit of account access through IP tracking
- Implement proper access controls for EasyAdmin interfaces

## License

MIT