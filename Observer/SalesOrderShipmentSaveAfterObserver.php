<?php
declare(strict_types=1);

namespace DarshilTech\SmsNotification\Observer;

use DarshilTech\SmsNotification\Observer\MainObserver;
use Magento\Framework\Event\Observer;

class SalesOrderShipmentSaveAfterObserver extends MainObserver
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
        $event = $observer->getEvent();
        $shipment = $event->getShipment();
        $order = $shipment->getOrder();
        $eventName = $event->getName();
        $isEnable = $this->helperData->isEnable();

        // Add logic for sending SMS, etc.
    }
}
