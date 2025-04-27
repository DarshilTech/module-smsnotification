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
        $event = $observer->getEvent();
        $customer = $event->getCustomer();
        $eventName = $event->getName();
        $isEnable = $this->helperData->isEnable();
        if ($isEnable && $this->smsProvider) {
            $template = '';
            $template = $this->helperData->getSmsTemplate($eventName);
            // $variables = $this->helperData->getCustomerTemplateVariables( $event);
            // $customer_number = $customer->getTelephone();
            // $customer_country_code = $customer->getCountryId();
            // if ($template) {
            //     $message = str_replace(array_keys($variables), array_values($variables), $template);
            //     $result = $this->smsProvider->sendSms($customer_number, $message);

            //     // Log the result
            //     $resultLog = $this->helperData->createLogs($event, $result);
            //     $smsLog = $this->smsLog;
            //     $smsLog->setData($resultLog);
            //     $smsLog->save();
            // }
        }

        // Add logic for sending SMS, etc.
    }
}
