<?php

namespace DarshilTech\SmsNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use DarshilTech\SmsNotification\Model\SmsService;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

class Validate extends Action implements HttpPostActionInterface, HttpGetActionInterface
{
    protected $resultJsonFactory;
    protected $layoutFactory;
    protected $smsServiceFactory;
    protected $customMessage = [];
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        SmsService $smsServiceFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->smsServiceFactory = $smsServiceFactory;
        $this->layoutFactory = $layoutFactory;
    }
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
    
        $data = $this->getRequest()->getPostValue();
        try {
            if (!$this->_isValidate($data)) {
                return $resultJson->setData([
                    'error' => true,
                    'message' => __('Template is already available in your library. You can change the content of it.'),
                ]);
            }
    
            // If validation passes
            return $resultJson->setData([
                'error' => false,
                'message' => __('Template is valid.'),
            ]);
        } catch (\Exception $e) {
            return $resultJson->setData([
                'error' => true,
                'message' => __('An error occurred: ') . $e->getMessage(),
            ]);
        }
    }
    
    protected function _isValidate($data)
    {
        $collection = $this->smsServiceFactory->getCollection()
            ->addFieldToFilter('event', $data['event']);
    
        return ($collection->getSize() <= 1);
    }
    
}