<?php

namespace AlipayBillBundle\Command;

use Alipay\OpenAPISDK\Api\AlipayDataDataserviceBillDownloadurlApi;
use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;
use AlipayBillBundle\Entity\Account;
use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Enum\BillType;
use AlipayBillBundle\Repository\AccountRepository;
use AlipayBillBundle\Repository\BillUrlRepository;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use HttpClientBundle\Service\SmartHttpClient;
use League\Flysystem\FilesystemOperator;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tourze\FileNameGenerator\RandomNameGenerator;
use Tourze\Symfony\CronJob\Attribute\AsCronTask;

/**
 * 账单下载
 *
 * @see https://opendocs.alipay.com/open/3c9f1bcf_alipay.data.dataservice.bill.downloadurl.query?pathHash=97357e8b&scene=common&ref=api
 */
#[AsCronTask('0 9 * * *')]
#[AsCronTask('0 10 * * *')]
#[AsCommand(name: 'alipay-trade:download-bill', description: '账单下载')]
class DownloadBillCommand extends Command
{
    public const NAME = 'alipay-trade:download-bill';
    public function __construct(
        private readonly AccountRepository $accountRepository,
        private readonly BillUrlRepository $billUrlRepository,
        private readonly LoggerInterface $logger,
        private readonly RandomNameGenerator $randomNameGenerator,
        private readonly FilesystemOperator $filesystem,
        private readonly SmartHttpClient $httpClient,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    private function getAlipayConfigUtil(Account $account): AlipayConfigUtil
    {
        // 设置alipayConfig参数（全局设置一次）
        $alipayConfig = new AlipayConfig();
        // 设置应用ID
        $alipayConfig->setAppId($account->getAppId());
        // 设置应用私钥
        $alipayConfig->setPrivateKey($account->getRsaPrivateKey());
        // 设置支付宝公钥
        $alipayConfig->setAlipayPublicKey($account->getRsaPublicKey());

        return new AlipayConfigUtil($alipayConfig);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // 基本上，没办法下载当天的数据，所以我们总是下载昨天的
        $date = Carbon::yesterday()->startOfDay();

        foreach ($this->accountRepository->findBy(['valid' => true]) as $account) {
            // 实例化客户端
            $api = new AlipayDataDataserviceBillDownloadurlApi();
            $api->setAlipayConfigUtil($this->getAlipayConfigUtil($account));

            foreach (BillType::cases() as $billType) {
                try {
                    $result = $api->query($billType->value, $date->format('Y-m-d'));
                } catch (\Throwable $exception) {
                    $this->logger->error('查询支付宝账号时发生异常', [
                        'account' => $account,
                        'date' => $date,
                        'billType' => $billType,
                        'exception' => $exception,
                    ]);
                    continue;
                }
                // 遇到这个，说明没有账单喔
                if ('EMPTY_DATA_WITH_BILL_FILE' === $result->getBillFileCode()) {
                    $this->logger->warning('查询不到支付宝账单', [
                        'account' => $account,
                        'date' => $date,
                        'billType' => $billType,
                        'result' => $result->jsonSerialize(),
                    ]);
                    continue;
                }

                $billUrl = $this->billUrlRepository->findOneBy([
                    'account' => $account,
                    'date' => $date,
                    'type' => $billType,
                ]);
                if (null === $billUrl) {
                    $billUrl = new BillUrl();
                    $billUrl->setAccount($account);
                    $billUrl->setDate($date);
                    $billUrl->setType($billType);
                }
                $billUrl->setDownloadUrl($result->getBillDownloadUrl());

                // 账单下载地址链接，获取连接后30秒后未下载，链接地址失效。
                // 下载的账单必然是一个zip包
                // @see https://opensupport.alipay.com/support/FAQ/13f8c849-cccf-400a-9543-f2a844ef9167
                $billData = $this->httpClient->request('GET', $billUrl->getDownloadUrl())->getContent();
                $key = $this->randomNameGenerator->generateDateFileName('zip', 'alipay-bill');
                $this->filesystem->write($key, $billData);
                $billUrl->setLocalFile($key);

                $this->entityManager->persist($billUrl);
                $this->entityManager->flush();
            }
        }

        return Command::SUCCESS;
    }
}
