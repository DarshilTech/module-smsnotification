<?php

namespace DarshilTech\SmsNotification\Block\Form;

use Magento\Customer\Model\AccountManagement;
use Magento\Store\Model\ScopeInterface;
use \Magento\Framework\Serialize\Serializer\Json;
use DarshilTech\SmsNotification\Helper\Data as HelperData;

class Register extends \Magento\Directory\Block\Data
{
    const COUNTRIES_ALLOWED = 'smsnotification/generaltelephone/allow';
    const DETECT_BY_IP = 'smsnotification/generaltelephone/detect_by_ip';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Registration
     *
     * @var \Magento\Customer\Model\Registration
     */
    protected $registration;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Registration $registration
     * @param \Magento\Framework\DataObjectFactory $objectFactory
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    protected $objectFactory;
    protected $jsonHelper;
    protected $helper;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        HelperData $helper,
        Json $jsonHelper,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        \Magento\Framework\DataObjectFactory $objectFactory,
        array $data = []
    ) {
        $this->objectFactory = $objectFactory;
        $this->jsonHelper = $jsonHelper;
        $this->moduleManager = $moduleManager;
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->registration = $registration;
        $this->helper = $helper;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
    }

    /**
     * Return registration
     *
     * @return \Magento\Customer\Model\Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * Retrieve back URL
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/login');
    }

    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $formData = $this->customerSession->getCustomerFormData(true);
            $data = $this->objectFactory->create();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int) $data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
        return $this->moduleManager->isOutputEnabled('Magento_Newsletter');
    }

    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getMinimumPasswordLength()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }

    /**
     * Get number of password required character classes
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function customerIsAlreadyLoggedIn()
    {
        return (bool) $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return bool|string
     */
    public function getPhoneconfig()
    {
        $ipBased = $this->_scopeConfig->getValue(self::DETECT_BY_IP, ScopeInterface::SCOPE_STORE);
        if ($ipBased) {
            $country = $this->helper->getCustomerIPDetails();
            if ($country) {
                $country = strtolower($country);
                return $this->jsonHelper->serialize([
                    "nationalMode" => false,
                    "utilsScript"  => $this->getViewFileUrl('DarshilTech_SmsNotification::js/utils.js'),
                    "preferredCountries" => [$country]
                ]);
            }
        }

        $config  = [
            "nationalMode" => false,
            "utilsScript"  => $this->getViewFileUrl('DarshilTech_SmsNotification::js/utils.js'),
            "preferredCountries" => ['in']
        ];

        $allowedCountries = $this->_scopeConfig->getValue(self::COUNTRIES_ALLOWED, ScopeInterface::SCOPE_STORE);
        $config["onlyCountries"] = explode(",", $allowedCountries??'');
        return $this->jsonHelper->serialize($config);
    }
}
