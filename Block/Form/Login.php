<?php

namespace DarshilTech\SmsNotification\Block\Form;

use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;
use DarshilTech\SmsNotification\Helper\Data as HelperData;

class Login extends \Magento\Framework\View\Element\Template
{
    const COUNTRIES_ALLOWED = 'smsnotification/generaltelephone/allow';
    const DETECT_BY_IP = 'smsnotification/generaltelephone/detect_by_ip';


    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

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
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Registration $registration
     * @param array $data
     */
    protected $jsonHelper;
    protected $helper;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Registration $registration,
        HelperData $helper,
        Json $jsonHelper,
        array $data = []
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        parent::__construct($context, $data);
        $this->customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->registration = $registration;
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
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('customer/ajax/login');
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
     * Retrieve registering URL
     *
     * @return string
     */
    public function getCustomerRegistrationUrl()
    {
        return $this->getUrl('customer/account/create');
    }

    /**
     * @return bool|string
     */
    public function getPhoneconfig()
    {
        $ipBased = $this->_scopeConfig->getValue(self::DETECT_BY_IP, ScopeInterface::SCOPE_STORE);
        if ($ipBased) {
            $country  = $this->helper->getCustomerIPDetails();;
            if ($country) {
                $country = strtolower($country);
                return $this->jsonHelper->serialize([
                    "nationalMode" => false,
                    "utilsScript" => $this->getViewFileUrl('DarshilTech_SmsNotification::js/utils.js'),
                    "preferredCountries" => [$country]
                ]);
            }
        }

        $config = [
            "nationalMode" => false,
            "utilsScript" => $this->getViewFileUrl('DarshilTech_SmsNotification::js/utils.js'),
            "preferredCountries" => ['in'],
        ];

        $allowedCountries = $this->_scopeConfig->getValue(self::COUNTRIES_ALLOWED, ScopeInterface::SCOPE_STORE);
        $config["onlyCountries"] = explode(",", $allowedCountries ?? '');
        return $this->jsonHelper->serialize($config);
    }
}
