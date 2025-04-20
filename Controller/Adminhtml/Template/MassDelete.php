<?php

namespace DarshilTech\SmsNotification\Controller\Adminhtml\Template;

use Magento\Backend\App\Action;
use Magento\Ui\Component\MassAction\Filter;
use DarshilTech\SmsNotification\Model\ResourceModel\SmsService\CollectionFactory;

class MassDelete extends Action
{
    protected $filter;
    protected $collectionFactory;
    protected $helper;

    public function __construct(
        Action\Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $deletedCount = 0;

        try {
            foreach ($collection as $template) {
                $template->delete();
                $deletedCount++;
            }

            if ($deletedCount) {
                $this->messageManager->addSuccessMessage(__('A total of %1 template(s) have been deleted.', $deletedCount));
            } else {
                $this->messageManager->addNoticeMessage(__('No template(s) were deleted.'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('Error deleting template: ') . $e->getMessage());
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}