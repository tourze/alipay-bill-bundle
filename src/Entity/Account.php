<?php

namespace AlipayBillBundle\Entity;

use AlipayBillBundle\Repository\AccountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Traits\IpTraceableAware;
use Tourze\DoctrineSnowflakeBundle\Traits\SnowflakeKeyAware;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(name: 'alipay_fund_auth_trade_account', options: ['comment' => '支付宝账号'])]
class Account implements \Stringable
{
    use SnowflakeKeyAware;
    use TimestampableAware;
    use BlameableAware;
    use IpTraceableAware;

    #[Assert\NotBlank]
    #[Assert\Length(max: 32)]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 32, unique: true, options: ['comment' => '名称'])]
    private string $name;

    #[Assert\Length(max: 64)]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 64, unique: true, options: ['comment' => 'AppID'])]
    private ?string $appId = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'RSA私钥'])]
    private ?string $rsaPrivateKey = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => 'RSA公钥'])]
    private ?string $rsaPublicKey = null;

    #[Assert\NotNull]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效'])]
    private ?bool $valid = false;

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

    public function setValid(?bool $valid): void
    {
        $this->valid = $valid;
    }

    public function getAppId(): ?string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getRsaPrivateKey(): ?string
    {
        return $this->rsaPrivateKey;
    }

    public function setRsaPrivateKey(?string $rsaPrivateKey): void
    {
        $this->rsaPrivateKey = $rsaPrivateKey;
    }

    public function getRsaPublicKey(): ?string
    {
        return $this->rsaPublicKey;
    }

    public function setRsaPublicKey(?string $rsaPublicKey): void
    {
        $this->rsaPublicKey = $rsaPublicKey;
    }
}
