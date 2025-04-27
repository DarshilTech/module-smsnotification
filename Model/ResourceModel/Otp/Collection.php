<?php

namespace DarshilTech\SmsNotification\Model\ResourceModel\Otp;

/**
 * Class Collection
 * @package DarshilTech\SmsNotification\Model\ResourceModel\Otp
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('DarshilTech\SmsNotification\Model\Otp', 'DarshilTech\SmsNotification\Model\ResourceModel\Otp');
	}
}
