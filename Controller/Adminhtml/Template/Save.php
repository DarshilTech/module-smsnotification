<?php

namespace DarshilTech\SmsNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action
{
    protected $smsServiceFactory;
    protected $customMessage = [];
    protected $pId = [];

    public function __construct(
        Action\Context $context,
        \DarshilTech\SmsNotification\Model\SmsService $smsServiceFactory
    ) {
        parent::__construct($context);
        $this->smsServiceFactory = $smsServiceFactory;
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $template = $this->smsServiceFactory;
                if (isset($data['entity_id']) && !empty($data['entity_id'])) {
                    $template->load($data['entity_id']);
                    $data['updated_at'] = date('Y-m-d H:i:s');
                } else {
                    unset($data['entity_id']);
                }
                $template->setData($data);

                $template->save();
                $this->messageManager->addSuccessMessage(__('SMS Template saved successfully.'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage('Something went wrong');
            }
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $template->getId()]);
            }
        }
        return $this->_redirect('*/*/');
    }
   
}