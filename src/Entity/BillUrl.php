<?php

namespace AlipayBillBundle\Entity;

use AlipayBillBundle\Enum\BillType;
use AlipayBillBundle\Repository\BillUrlRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: BillUrlRepository::class)]
#[ORM\Table(name: 'alipay_trade_bill_url', options: ['comment' => '账单URL'])]
#[ORM\UniqueConstraint(name: 'alipay_trade_bill_url_idx_uniq', columns: ['account_id', 'type', 'date'])]
class BillUrl implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Account $account;

    #[Assert\Choice(callback: [BillType::class, 'cases'])]
    #[Assert\NotNull]
    #[IndexColumn]
    #[ORM\Column(length: 40, enumType: BillType::class, options: ['comment' => '账单类型'])]
    private BillType $type = BillType::trade;

    #[Assert\NotNull]
    #[IndexColumn]
    #[ORM\Column(type: Types::DATE_IMMUTABLE, options: ['comment' => '日期'])]
    private \DateTimeInterface $date;

    #[Assert\NotBlank]
    #[Assert\Length(max: 1000)]
    #[Assert\Url]
    #[ORM\Column(length: 1000, options: ['comment' => '原始下载地址'])]
    private string $downloadUrl;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(length: 1000, nullable: true, options: ['comment' => '本地下载地址'])]
    private ?string $localFile = null;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return sprintf('%s - %s - %s',
            $this->getAccount()->getName(),
            $this->getType()->value,
            $this->getDate()->format('Y-m-d')
        );
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

    public function setAccount(Account $account): void
    {
        $this->account = $account;
    }

    public function getDownloadUrl(): string
    {
        return $this->downloadUrl;
    }

    public function setDownloadUrl(string $downloadUrl): void
    {
        $this->downloadUrl = $downloadUrl;
    }

    public function getLocalFile(): ?string
    {
        return $this->localFile;
    }

    public function setLocalFile(?string $localFile): void
    {
        $this->localFile = $localFile;
    }

    public function getType(): BillType
    {
        return $this->type;
    }

    public function setType(BillType $type): void
    {
        $this->type = $type;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }
}
