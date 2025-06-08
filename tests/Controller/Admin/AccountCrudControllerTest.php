<?php

namespace AlipayBillBundle\Tests\Controller\Admin;

use AlipayBillBundle\Controller\Admin\AccountCrudController;
use AlipayBillBundle\Entity\Account;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class AccountCrudControllerTest extends TestCase
{
    private AccountCrudController $controller;

    protected function setUp(): void
    {
        $this->controller = new AccountCrudController();
    }

    public function testInstanceOfAbstractCrudController()
    {
        $this->assertInstanceOf(AbstractCrudController::class, $this->controller);
    }

    public function testGetEntityFqcn()
    {
        $this->assertSame(Account::class, AccountCrudController::getEntityFqcn());
    }

    public function testConfigureCrud()
    {
        $crud = $this->controller->configureCrud(Crud::new());

        $this->assertInstanceOf(Crud::class, $crud);
    }

    public function testConfigureFields()
    {
        $fields = iterator_to_array($this->controller->configureFields(Crud::PAGE_INDEX));

        $this->assertNotEmpty($fields);
        $this->assertGreaterThan(0, count($fields));
    }

    public function testConfigureActions()
    {
        $actions = $this->controller->configureActions(
            \EasyCorp\Bundle\EasyAdminBundle\Config\Actions::new()
        );

        $this->assertInstanceOf(\EasyCorp\Bundle\EasyAdminBundle\Config\Actions::class, $actions);
    }

    public function testConfigureFilters()
    {
        $filters = $this->controller->configureFilters(
            \EasyCorp\Bundle\EasyAdminBundle\Config\Filters::new()
        );

        $this->assertInstanceOf(\EasyCorp\Bundle\EasyAdminBundle\Config\Filters::class, $filters);
    }

    public function testHasAdminCrudAttribute()
    {
        $reflection = new ReflectionClass(AccountCrudController::class);
        $attributes = $reflection->getAttributes();

        $hasAdminCrudAttribute = false;
        foreach ($attributes as $attribute) {
            if (str_contains($attribute->getName(), 'AdminCrud')) {
                $hasAdminCrudAttribute = true;
                break;
            }
        }

        $this->assertTrue($hasAdminCrudAttribute, 'Controller should have AdminCrud attribute');
    }
} 