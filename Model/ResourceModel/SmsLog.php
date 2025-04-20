<?php

namespace DarshilTech\SmsNotification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SmsLog extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('darshiltech_sms_log', 'entity_id'); // Table name and primary key
    }
}
