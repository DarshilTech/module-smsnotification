<?php
namespace DarshilTech\SmsNotification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SmsService extends AbstractDb
{
    public const TABLE_NAME = 'darshiltech_sms_service';
    protected function _construct()
    {
        $this->_init(self::TABLE_NAME, 'entity_id');
    }
}
