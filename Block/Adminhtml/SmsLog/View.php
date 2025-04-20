<?php

namespace DarshilTech\SmsNotification\Block\Adminhtml\SmsLog;

use Magento\Backend\Block\Template;
use DarshilTech\SmsNotification\Model\SmsLog;
use DarshilTech\SmsNotification\Model\Config\Source\Events;
use DarshilTech\SmsNotification\Model\Config\Source\ProviderList;
class View extends Template
{
    protected $_template = "DarshilTech_SmsNotification::log/view.phtml";
    protected $smsLog;
    protected $events;
    protected $providerList;
    public function __construct(
        Template\Context $context,
        Events $events,
        ProviderList $providerList,
        SmsLog $smsLog,
        array $data = []
    ) {
        $this->smsLog = $smsLog;
        $this->events = $events;
        $this->providerList = $providerList;
        parent::__construct($context, $data);
    }
    public function getLogData()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $logData = $this->smsLog->load($id);
            return $logData;
        }
    }
   public function getEvents(){
    return $this->events->toOptionArray();
   }
    public function getProviderList(){
     return $this->providerList->toOptionArray();
    }
}