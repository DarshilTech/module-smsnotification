<?php
declare(strict_types=1);

namespace DarshilTech\SmsNotification\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;

class AddCustomCustomerAttribute implements DataPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;
    private CustomerSetupFactory $customerSetupFactory;
    private EavConfig $eavConfig;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        CustomerSetupFactory $customerSetupFactory,
        EavConfig $eavConfig
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $customerSetup = $this->customerSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeCode = 'customer_mobile_number';
        $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, [
            'label' => 'Customer Mobile Number',
            'input' => 'text',
            'required' => false,
            'sort_order' => 150,
            'position' => 150,
            'visible' => true,
            'system' => false,
            'unique' => true,
            'is_used_in_grid' => true,
            'is_visible_in_grid' => true,
            'is_filterable_in_grid' => true,
            'is_searchable_in_grid' => false
        ]);
        $attribute = $this->eavConfig->getAttribute(Customer::ENTITY, $attributeCode);
        $usedInForms = [
            'adminhtml_customer',
            'checkout_register',
            'customer_account_create',
            'customer_account_edit',
            'adminhtml_checkout'
        ];
        $attribute->setData('used_in_forms', $usedInForms)
        ->setData('is_used_for_customer_segment', true)
        ->setData('is_system', 0)
        ->setData('is_user_defined', 1);
        $attribute->save();

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
