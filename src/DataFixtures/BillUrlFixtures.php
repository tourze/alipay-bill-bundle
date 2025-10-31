<?php

namespace AlipayBillBundle\DataFixtures;

use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Enum\BillType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\DependencyInjection\Attribute\When;

#[When(env: 'test')]
#[When(env: 'dev')]
class BillUrlFixtures extends Fixture implements DependentFixtureInterface
{
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('zh_CN');
    }

    public function load(ObjectManager $manager): void
    {
        $billTypes = BillType::cases();
        $createdCombinations = [];

        for ($i = 1; $i <= 10; ++$i) {
            /** @var Account $account */
            $account = $this->getReference('alipay-account-' . $this->faker->numberBetween(1, 3), Account::class);
            /** @var BillType $type */
            $type = $this->faker->randomElement($billTypes);

            // 生成唯一的日期来避免约束冲突
            $dateAttempts = 0;
            do {
                $date = new \DateTimeImmutable($this->faker->dateTimeBetween('-60 days', 'now')->format('Y-m-d'));
                $combinationKey = (string) $account->getId() . '_' . $type->value . '_' . $date->format('Y-m-d');
                ++$dateAttempts;

                // 如果尝试超过20次仍然重复，使用当前时间戳确保唯一性
                if ($dateAttempts > 20) {
                    $date = new \DateTimeImmutable('2024-01-' . sprintf('%02d', $i));
                    $combinationKey = (string) $account->getId() . '_' . $type->value . '_' . $date->format('Y-m-d');
                    break;
                }
            } while (isset($createdCombinations[$combinationKey]));

            $createdCombinations[$combinationKey] = true;

            $billUrl = new BillUrl();
            $billUrl->setAccount($account);
            $billUrl->setType($type);
            $billUrl->setDate($date);
            $billUrl->setDownloadUrl($this->faker->url() . '/bill/' . $this->faker->uuid() . '.zip');

            if ($this->faker->boolean(60)) {
                $billUrl->setLocalFile('/storage/alipay/bills/' . $this->faker->uuid() . '.zip');
            }

            $manager->persist($billUrl);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AccountFixtures::class,
        ];
    }
}
