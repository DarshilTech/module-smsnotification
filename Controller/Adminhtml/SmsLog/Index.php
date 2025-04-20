<?php
namespace DarshilTech\SmsNotification\Controller\Adminhtml\SmsLog;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'DarshilTech_SmsNotification::sms_log';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    public function __construct(Action\Context $context, PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('DarshilTech_SmsNotification::sms_log');
        $resultPage->getConfig()->getTitle()->prepend(__('SMS Logs'));
        return $resultPage;
    }
}
