<?php

namespace DarshilTech\SmsNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use DarshilTech\SmsNotification\Model\SmsServiceFactory;

class Edit extends Action
{
    protected $resultPageFactory;
    protected $smsServiceFactory;
    protected $coreRegistry;

    public function __construct(
        Action\Context $context,
        PageFactory $resultPageFactory,
        SmsServiceFactory $smsServiceFactory,
        Registry $coreRegistry
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->smsServiceFactory = $smsServiceFactory;
        $this->coreRegistry = $coreRegistry;
    }

    public function execute()
    {
        $tId = $this->getRequest()->getParam('id');
        $template = $this->smsServiceFactory->create();

        if ($tId) {
            $template->load($tId);
            if (!$template->getId()) {
                $this->messageManager->addErrorMessage(__('This template no longer exists.'));
                return $this->_redirect('*/*/');
            }
            $this->coreRegistry->register('sms_template', $template);
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__($template->getTemplateName() ?? 'New Template'));

        return $resultPage;
    }
}