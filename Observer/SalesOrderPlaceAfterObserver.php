<?php
declare(strict_types=1);

namespace DarshilTech\SmsNotification\Observer;

use DarshilTech\SmsNotification\Observer\MainObserver;
use Magento\Framework\Event\Observer;
class SalesOrderPlaceAfterObserver extends MainObserver
{

    /**
     * Execute the observer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        try {
            $event = $observer->getEvent();
            $order = $event->getOrder();
            $eventName = $event->getName();
            $isEnable = $this->helperData->isEnable();

            if ($isEnable && $this->smsProvider) {
                $template = '';

                // Check if the customer is logged in
                if ((int)$order->getCustomerIsGuest()) {
                    // If logged in, get template for logged-in users
                    $template = $this->helperData->getSmsTemplate($eventName);
                } else {
                    // If not logged in, get template for guest users
                    $eventName = $eventName . '_is_guest';
                    $template = $this->helperData->getSmsTemplate($eventName);
                }

                $customer_number = $order->getShippingAddress()->getTelephone();
                $customer_country_code = $order->getShippingAddress()->getCountryId();
                if ($customer_country_code) {
                    $customer_number = $this->helperData->getCountryCode($customer_country_code) . $customer_number;
                }
                if (!$customer_number) {
                    return;
                }

                // Dynamic data population
                $variables = $this->helperData->getDynamicVariables('order', $event);


                if ($template) {
                    $message = str_replace(array_keys($variables), array_values($variables), $template);
                    $result = $this->smsProvider->sendSms($customer_number, $message);

                    // Log the result
                    $resultLog = $this->helperData->createLogs($event, $result);
                    $smsLog = $this->smsLog;
                    $smsLog->setData($resultLog);
                    $smsLog->save();
                }
            }
        } catch (\Exception $e) {
            return;
        }
    }

}
