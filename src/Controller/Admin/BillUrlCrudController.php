<?php

namespace AlipayBillBundle\Controller\Admin;

use AlipayBillBundle\Entity\BillUrl;
use AlipayBillBundle\Enum\BillType;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\DateTimeFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

/**
 * 支付宝账单管理
 */
#[AdminCrud(routePath: '/alipay-bill/bill-url', routeName: 'alipay_bill_bill_url')]
class BillUrlCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BillUrl::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('支付宝账单')
            ->setEntityLabelInPlural('支付宝账单管理')
            ->setPageTitle('index', '支付宝账单列表')
            ->setPageTitle('detail', '查看支付宝账单')
            ->setHelp('index', '管理支付宝账单下载记录，查看各类型账单的下载情况')
            ->setDefaultSort(['id' => 'DESC'])
            ->setSearchFields(['account.name', 'account.appId']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')
            ->setMaxLength(9999)
            ->onlyOnIndex();

        yield AssociationField::new('account', '支付宝应用')
            ->setRequired(true)
            ->formatValue(function ($value) {
                return $value ? $value->getName() : '';
            });

        yield ChoiceField::new('type', '账单类型')
            ->setFormType(EnumType::class)
            ->setFormTypeOptions(['class' => BillType::class])
            ->formatValue(function ($value) {
                return $value instanceof BillType ? $value->getLabel() : '';
            })
            ->setRequired(true);

        yield DateField::new('date', '账单日期')
            ->setRequired(true)
            ->setHelp('账单对应的日期');

        yield UrlField::new('downloadUrl', '原始下载地址')
            ->hideOnIndex()
            ->setHelp('支付宝提供的临时下载链接，30秒后失效');

        yield TextField::new('localFile', '本地文件')
            ->setHelp('已下载到本地存储的文件路径')
            ->formatValue(function ($value) {
                if (!$value) {
                    return '<span class="badge badge-warning">未下载</span>';
                }
                return '<span class="badge badge-success">已下载</span><br><small>' . $value . '</small>';
            });

        yield DateTimeField::new('createTime', '创建时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : '';
            });

        yield DateTimeField::new('updateTime', '更新时间')
            ->hideOnForm()
            ->formatValue(function ($value) {
                return $value instanceof \DateTimeInterface ? $value->format('Y-m-d H:i:s') : '';
            });
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->disable(Action::NEW, Action::EDIT, Action::DELETE);
    }

    public function configureFilters(Filters $filters): Filters
    {
        $choices = [];
        foreach (BillType::cases() as $case) {
            $choices[$case->getLabel()] = $case->value;
        }

        return $filters
            ->add(EntityFilter::new('account', '支付宝应用'))
            ->add(ChoiceFilter::new('type', '账单类型')->setChoices($choices))
            ->add(DateTimeFilter::new('createTime', '创建时间'))
            ->add(DateTimeFilter::new('updateTime', '更新时间'));
    }
} 