<?php

namespace DarshilTech\SmsNotification\Observer;


use Magento\Framework\Event\ObserverInterface;
use DarshilTech\SmsNotification\Model\Provider\TwilioProvider;
use DarshilTech\SmsNotification\Helper\Data;
use DarshilTech\SmsNotification\Model\SmsLog;

abstract class MainObserver implements ObserverInterface
{
    protected $helperData;
    protected $smsProvider;
    protected $smsLog;


    public function __construct(
        Data $helperData,
        SmsLog $smsLog
    ) {
        $this->helperData = $helperData;
        $this->smsLog = $smsLog;

        // Initialize the SMS provider based on configuration
        $provider = $this->helperData->getSmsProvider();
        if ($provider === 'twilio') {
            $this->smsProvider = \Magento\Framework\App\ObjectManager::getInstance()->create(TwilioProvider::class);
        }
    }

}