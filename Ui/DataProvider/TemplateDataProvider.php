<?php

namespace DarshilTech\SmsNotification\Ui\DataProvider;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use DarshilTech\SmsNotification\Model\ResourceModel\SmsService\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

class TemplateDataProvider extends  AbstractDataProvider
{
    protected $loadedData = [];
    protected $dataPersistor;

    public function __construct(
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
    }

    public function getData()
    {
        if (!$this->loadedData) {
            foreach ($this->collection->getItems() as $template) {
                $this->loadedData[$template->getId()] = $template->getData();
            }
        }
        $data = $this->dataPersistor->get('sms_template');

        if (!empty($data)) {
            $template = $this->collection->getNewEmptyItem();
            $template->setData($data);
            $this->loadedData[$template->getId()] = $template->getData();
            $this->dataPersistor->clear('sms_template');
        }
        return $this->loadedData;
    }
}