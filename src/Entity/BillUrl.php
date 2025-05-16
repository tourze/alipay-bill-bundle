<?php

namespace AlipayBillBundle\Entity;

use AlipayBillBundle\Enum\BillType;
use AlipayBillBundle\Repository\BillUrlRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Attribute\CreateTimeColumn;
use Tourze\DoctrineTimestampBundle\Attribute\UpdateTimeColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Filter\Filterable;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '支付宝账单')]
#[ORM\Entity(repositoryClass: BillUrlRepository::class)]
#[ORM\Table(name: 'alipay_trade_bill_url', options: ['comment' => '账单URL'])]
#[ORM\UniqueConstraint(name: 'alipay_trade_bill_url_idx_uniq', columns: ['account_id', 'type', 'date'])]
class BillUrl
{
    #[ListColumn(order: -1)]
    #[ExportColumn]
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ListColumn(title: '支付宝应用')]
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[ListColumn]
    #[IndexColumn]
    #[ORM\Column(length: 40, enumType: BillType::class, options: ['comment' => '账单类型'])]
    private BillType $type = BillType::trade;

    #[ListColumn]
    #[IndexColumn]
    #[ORM\Column(type: Types::DATE_MUTABLE, options: ['comment' => '日期'])]
    private \DateTimeInterface $date;

    #[ListColumn]
    #[ORM\Column(length: 1000, options: ['comment' => '原始下载地址'])]
    private string $downloadUrl;

    #[ListColumn]
    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '本地下载地址'])]
    private ?string $localFile = null;

    #[Filterable]
    #[IndexColumn]
    #[ListColumn(order: 98, sorter: true)]
    #[ExportColumn]
    #[CreateTimeColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '创建时间'])]
    private ?\DateTimeInterface $createTime = null;

    #[UpdateTimeColumn]
    #[ListColumn(order: 99, sorter: true)]
    #[Filterable]
    #[ExportColumn]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '更新时间'])]
    private ?\DateTimeInterface $updateTime = null;

    public function setCreateTime(?\DateTimeInterface $createdAt): void
    {
        $this->createTime = $createdAt;
    }

    public function getCreateTime(): ?\DateTimeInterface
    {
        return $this->createTime;
    }

    public function setUpdateTime(?\DateTimeInterface $updateTime): void
    {
        $this->updateTime = $updateTime;
    }

    public function getUpdateTime(): ?\DateTimeInterface
    {
        return $this->updateTime;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getAccount(): Account
    {
        return $this->account;
    }

    public function setAccount(Account $account): static
    {
        $this->account = $account;

        return $this;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(string $downloadUrl): static
    {
        $this->downloadUrl = $downloadUrl;

        return $this;
    }

    public function getLocalFile(): ?string
    {
        return $this->localFile;
    }

    public function setLocalFile(?string $localFile): static
    {
        $this->localFile = $localFile;

        return $this;
    }

    public function getType(): BillType
    {
        return $this->type;
    }

    public function setType(BillType $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }
}
