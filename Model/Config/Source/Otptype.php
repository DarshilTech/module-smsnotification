<?php

namespace DarshilTech\SmsNotification\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Otptype implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'number', 'label' => __('Number')],
            ['value' => 'alphabets', 'label' => __('Alphabets')],
            ['value' => 'alphanumeric', 'label' => __('Alphanumeric')]
        ];
    }
}
