<?php
namespace DarshilTech\SmsNotification\Api;

interface SmsMainInterface
{
    /**
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @param int|string $modelId
     * @param string|null $field
     * @return \DarshilTech\SmsNotification\Model\SmsService
     */
    public function load($modelId, $field = null);

    /**
     * @param string $value
     * @return $this
     */
    public function setTemplateName($value);

    /**
     * @return string|null
     */
    public function getTemplateName();

    /**
     * @param string $value
     * @return $this
     */
    public function setEvent($value);

    /**
     * @return string|null
     */
    public function getEvent();

    /**
     * @param string $value
     * @return $this
     */
    public function setTemplateContent($value);

    /**
     * @return string|null
     */
    public function getTemplateContent();
}
