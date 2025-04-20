<?php
namespace DarshilTech\SmsNotification\Model\ResourceModel\SmsService;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DarshilTech\SmsNotification\Model\SmsService as SmsServiceModel;
use DarshilTech\SmsNotification\Model\ResourceModel\SmsService as SmsServiceResourceModel;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'darshiltech_sms_service_collection';

    /**
     * Define model and resource model for the collection.
     */
    protected function _construct()
    {
        $this->_init(SmsServiceModel::class, SmsServiceResourceModel::class);
    }
}
