<?php

namespace AlipayBillBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 账单类型
 *
 * @see https://opendocs.alipay.com/open/3c9f1bcf_alipay.data.dataservice.bill.downloadurl.query?pathHash=97357e8b&scene=common&ref=api
 */
enum BillType: string implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case trade = 'trade';
    case signcustomer = 'signcustomer';
    case merchant_act = 'merchant_act';

    //    case trade_zft_merchant = 'trade_zft_merchant';
    //    case zft_acc = 'zft_acc';
    case settlementMerge = 'settlementMerge';

    public function getLabel(): string
    {
        return match ($this) {
            self::trade => '商户基于支付宝交易收单的业务账单',
            self::signcustomer => '基于商户支付宝余额收入及支出等资金变动的账务账单',
            self::merchant_act => '营销活动账单，包含营销活动的发放，核销记录',
            //            self::trade_zft_merchant => '直付通二级商户查询交易的业务账单',
            //            self::zft_acc => '直付通平台商查询二级商户流水使用，返回所有二级商户流水',
            self::settlementMerge => '每日结算到卡的资金对应的明细，下载内容包含批次结算到卡明细文件',
        };
    }
}
