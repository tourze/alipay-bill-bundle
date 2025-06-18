<?php

namespace AlipayBillBundle\Entity;

use AlipayBillBundle\Repository\AccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;
use Tourze\EasyAdmin\Attribute\Action\Listable;

#[Listable]
#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_account', options: ['comment' => '支付宝账号'])]
class Account implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true, options: ['comment' => '名称'])]
    private string $name;

    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => 'AppID'])]
    private ?string $appId = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'RSA私钥'])]
    private ?string $rsaPrivateKey = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'RSA公钥'])]
    private ?string $rsaPublicKey = null;

    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效'])]
    private ?bool $valid = false;


    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    use TimestampableAware;
    use BlameableAware;

    public function __toString(): string
    {
        if (null === $this->getId()) {
            return '';
        }

        return $this->getName();
    }


    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): self
    {
        $this->appId = $appId;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRsaPrivateKey(): ?string
    {
        return $this->rsaPrivateKey;
    }

    public function setRsaPrivateKey(?string $rsaPrivateKey): static
    {
        $this->rsaPrivateKey = $rsaPrivateKey;

        return $this;
    }

    public function getRsaPublicKey(): ?string
    {
        return $this->rsaPublicKey;
    }

    public function setRsaPublicKey(?string $rsaPublicKey): static
    {
        $this->rsaPublicKey = $rsaPublicKey;

        return $this;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }
}
