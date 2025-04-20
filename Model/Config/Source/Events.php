<?php
namespace DarshilTech\SmsNotification\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Events implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'customer_register_success',
                'label' => __('Customer Registration Success')
            ],
            // [
            //     'value' => 'customer_forgotpassword_success',
            //     'label' => __('Customer Forgot Password Success')
            // ],
            [
                'value' => 'sales_order_place_after_is_guest',
                'label' => __('New Order for Guest')
            ],
            [
                'value' => 'sales_order_place_after',
                'label' => __('New Order')
            ],
            [
                'value' => 'sales_order_invoice_save_after_is_guest',
                'label' => __('Invoice Update for Guest')
            ],
            [
                'value' => 'sales_order_invoice_save_after',
                'label' => __('Invoice Update')
            ],
            [
                'value' => 'sales_order_creditmemo_save_after_is_guest',
                'label' => __('Credit Memo Update for Guest')
            ],
            [
                'value' => 'sales_order_creditmemo_save_after',
                'label' => __('Credit Memo Update')
            ],
            [
                'value' => 'sales_order_shipment_save_after_is_guest',
                'label' => __('Shipment Update for Guest')
            ],
            [
                'value' => 'sales_order_shipment_save_after',
                'label' => __('Shipment Update')
            ]
        ];
    }
}
