<?php

namespace DarshilTech\SmsNotification\Block\Adminhtml\SMS\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Backend\Block\Widget\Context;

class DeleteButton implements ButtonProviderInterface
{
    protected $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getButtonData()
    {
        $supplierId = $this->context->getRequest()->getParam('id');

        if (!$supplierId) {
            return [];
        }

        return [
            'label' => __('Delete'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __('Are you sure you want to delete this template ?') . '\', \'' . $this->getDeleteUrl() . '\')',
            'sort_order' => 20
        ];
    }

    protected function getDeleteUrl()
    {
        return $this->context->getUrlBuilder()->getUrl('*/*/delete', ['id' => $this->context->getRequest()->getParam('id')]);
    }
}