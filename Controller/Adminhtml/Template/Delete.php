<?php 

namespace DarshilTech\SmsNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use DarshilTech\SmsNotification\Model\SmsService;

class Delete extends Action
{
    protected $smsService;

    public function __construct(
        Action\Context $context,
        SmsService $smsService
    ) {
        parent::__construct($context);
        $this->smsService = $smsService;
    }

    public function execute()
    {
        $tId = $this->getRequest()->getParam('id');

        if ($tId) {
            try {
                $template = $this->smsService->create()->load($tId);
                
                if (!$template->getId()) {
                    throw new LocalizedException(__('This template no longer exists.'));
                }

                $template->delete();
                $this->messageManager->addSuccessMessage(__('The template has been deleted successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Error while deleting supplier: ') . $e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage(__('Invalid template ID.'));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/');
    }
}