<?php
declare(strict_types=1);

namespace DarshilTech\SmsNotification\Observer;

use DarshilTech\SmsNotification\Observer\MainObserver;
use Magento\Framework\Event\Observer;

class CustomerRegisterSuccessObserver extends MainObserver
{
  
    /**
     * Execute the observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        // Get customer data or perform actions when the event is triggered
        $customer = $observer->getEvent()->getCustomer();
        // Add logic for sending SMS, etc.
    }
}
