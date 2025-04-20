<?php
namespace DarshilTech\SmsNotification\Model;

use Magento\Framework\Model\AbstractModel;
use DarshilTech\SmsNotification\Model\ResourceModel\SmsService as SmsServiceResource;
use DarshilTech\SmsNotification\Api\SmsMainInterface;

class SmsService extends AbstractModel implements SmsMainInterface
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'darshiltech_sms_service';

    protected function _construct()
    {
        $this->_init(SmsServiceResource::class);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        return $this->setData('entity_id', $id);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData('entity_id');
    }

    /**
     * {@inheritdoc}
     */
    public function load($modelId, $field = null)
    {
        return parent::load($modelId, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateName($value)
    {
        return $this->setData('template_name', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateName()
    {
        return $this->getData('template_name');
    }

    /**
     * {@inheritdoc}
     */
    public function setEvent($value)
    {
        return $this->setData('event', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getEvent()
    {
        return $this->getData('event');
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateContent($value)
    {
        return $this->setData('template_content', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateContent()
    {
        return $this->getData('template_content');
    }
}
