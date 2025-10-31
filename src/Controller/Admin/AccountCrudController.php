<?php

namespace AlipayBillBundle\Controller\Admin;

use AlipayBillBundle\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;

/**
 * 支付宝账号管理
 *
 * @extends AbstractCrudController<Account>
 */
#[AdminCrud(routePath: '/alipay-bill/account', routeName: 'alipay_bill_account')]
final class AccountCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Account::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('支付宝账号')
            ->setEntityLabelInPlural('支付宝账号管理')
            ->setPageTitle('index', '支付宝账号列表')
            ->setPageTitle('new', '新增支付宝账号')
            ->setPageTitle('edit', '编辑支付宝账号')
            ->setPageTitle('detail', '查看支付宝账号')
            ->setHelp('index', '管理支付宝账号配置，用于下载账单数据')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['name', 'appId'])
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->onlyOnIndex()
        ;

        yield TextField::new('name', '名称')
            ->setRequired(true)
            ->setHelp('账号的标识名称，用于区分不同的支付宝应用')
        ;

        yield TextField::new('appId', 'AppID')
            ->setRequired(true)
            ->setHelp('支付宝应用的AppID')
        ;

        yield TextareaField::new('rsaPrivateKey', '应用私钥')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('应用的RSA私钥，用于API调用签名')
        ;

        yield TextareaField::new('rsaPublicKey', '支付宝公钥')
            ->setRequired(false)
            ->hideOnIndex()
            ->setHelp('支付宝的RSA公钥，用于验证签名')
        ;

        yield BooleanField::new('valid', '有效状态')
            ->setHelp('是否启用该账号进行账单下载')
        ;

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : '';
            })
        ;

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : '';
            })
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name', '名称'))
            ->add(TextFilter::new('appId', 'AppID'))
            ->add(BooleanFilter::new('valid', '有效状态'))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'))
        ;
    }
}
