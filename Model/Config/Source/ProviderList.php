<?php
namespace DarshilTech\SmsNotification\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ProviderList implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'twilio', 'label' => __('Twilio')],
            ['value' => 'textbelt', 'label' => __('Textbelt')],
            ['value' => 'callmebot', 'label' => __('CallMeBot')],
            ['value' => 'fast2sms', 'label' => __('Fast2SMS')],
            ['value' => 'custom', 'label' => __('Custom API')],
        ];
    }
}
