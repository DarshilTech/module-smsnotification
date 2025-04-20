<?php
namespace DarshilTech\SmsNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    const ADMIN_RESOURCE = 'DarshilTech_SmsNotification::sms_template';

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
        $resultPage->setActiveMenu('DarshilTech_SmsNotification::sms_template');
        $resultPage->getConfig()->getTitle()->prepend(__('SMS Templates'));
        return $resultPage;
    }
}
