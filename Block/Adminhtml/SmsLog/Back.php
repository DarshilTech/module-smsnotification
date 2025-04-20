<?php

namespace DarshilTech\SmsNotification\Block\Adminhtml\SmsLog;

use Magento\Backend\Block\Widget\Container;

class Back extends Container
{
    protected function _prepareLayout()
    {
        $backButtonProps = [
            'id' => 'back_button',
            'label' => __('Back'),
            'class' => 'back',
            'button_class' => 'secondary',
            'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
        ];
        $this->buttonList->add('back_button', $backButtonProps);

        return parent::_prepareLayout();
    }
    protected function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}