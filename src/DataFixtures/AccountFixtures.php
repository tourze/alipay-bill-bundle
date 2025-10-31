<?php

namespace AlipayBillBundle\DataFixtures;

use AlipayBillBundle\Entity\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class AccountFixtures extends Fixture
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('zh_CN');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 3; ++$i) {
            $account = new Account();
            $account->setName($this->faker->company() . '支付宝账号' . $i);
            $account->setAppId($this->faker->unique()->regexify('[0-9]{16}'));
            $account->setRsaPrivateKey($this->generateRsaKey('private'));
            $account->setRsaPublicKey($this->generateRsaKey('public'));
            $account->setValid($this->faker->boolean(80));

            $manager->persist($account);
            $this->addReference('alipay-account-' . $i, $account);
        }

        $manager->flush();
    }

    private function generateRsaKey(string $type): string
    {
        $header = 'private' === $type ? '-----BEGIN RSA PRIVATE KEY-----' : '-----BEGIN PUBLIC KEY-----';
        $footer = 'private' === $type ? '-----END RSA PRIVATE KEY-----' : '-----END PUBLIC KEY-----';

        $keyData = '';
        for ($i = 0; $i < 20; ++$i) {
            $keyData .= $this->faker->regexify('[A-Za-z0-9+/]{64}') . "\n";
        }

        return $header . "\n" . $keyData . $footer;
    }
}
