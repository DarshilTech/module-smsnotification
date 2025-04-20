<?php

namespace DarshilTech\SmsNotification\Model\ResourceModel\SmsLog;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use DarshilTech\SmsNotification\Model\SmsLog as Model;
use DarshilTech\SmsNotification\Model\ResourceModel\SmsLog as ResourceModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(Model::class, ResourceModel::class);
    }
}
