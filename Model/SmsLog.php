<?php

namespace DarshilTech\SmsNotification\Model;

use Magento\Framework\Model\AbstractModel;

class SmsLog extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(\DarshilTech\SmsNotification\Model\ResourceModel\SmsLog::class);
    }
}
