<?php

namespace DarshilTech\SmsNotification\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Otp
 * @package DarshilTech\SmsNotification\Model
 */
class Otp extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('DarshilTech\SmsNotification\Model\ResourceModel\Otp');
    }
}
