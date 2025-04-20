<?php
declare(strict_types=1);

namespace DarshilTech\SmsNotification\Ui\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Action
 */
class Action extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['entity_id'])) {
                    $editUrlPath = $this->getData('config/editUrlPath') ?: '#';
                    $deleteUrlPath = $this->getData('config/deleteUrlPath') ?: '#';
                    $viewUrlPath = $this->getData('config/viewUrlPath');
                    $urlEntityParamName = $this->getData('config/urlEntityParamName') ?: 'id';
                   
                    if ($viewUrlPath) {
                        $item[$this->getData('name')] = [
                            'view' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $viewUrlPath,
                                    [
                                        $urlEntityParamName => $item['entity_id']
                                    ]
                                ),
                                'label' => __('View')
                            ],
                        ];
                    } else {
                        $title = $item['template_name'];
                        $item[$this->getData('name')] = [
                            'edit' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $editUrlPath,
                                    [
                                        $urlEntityParamName => $item['entity_id']
                                    ]
                                ),
                                'label' => __('Edit')
                            ],
                            'delete' => [
                                'href' => $this->urlBuilder->getUrl(
                                    $deleteUrlPath,
                                    [
                                        $urlEntityParamName => $item['entity_id']
                                    ]
                                ),
                                'label' => __('Delete'),
                                'confirm' => [
                                    'title' => __('Delete record #%1', $title),
                                    'message' => __('Are you sure you want to delete this template ?')
                                ],
                                'post' => true
                            ]
                        ];
                    }
                }
            }
        }

        return $dataSource;
    }
}
