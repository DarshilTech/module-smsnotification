<?php

namespace DarshilTech\SmsNotification\Model\ResourceModel;

/**
 * Class Otp
 * @package DarshilTech\SmsNotification\Model\ResourceModel
 */
class Otp extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('darshiltech_mobile_otp', 'entity_id');
    }
}
