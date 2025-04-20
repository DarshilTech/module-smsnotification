<?php
namespace DarshilTech\SmsNotification\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use DarshilTech\SmsNotification\Model\SmsService;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Stdlib\DateTime;
use Magento\Customer\Model\Session as CustomerSession;
class Data extends AbstractHelper
{
    protected $encryptor;
    protected $smsService;
    protected $storeManager;
    protected $dateTimeFormatter;
    protected $customerSession;

    public function __construct(
        Context $context,
        SmsService $smsService,
        DateTime $dateTimeFormatter,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        CustomerSession $customerSession
    ) {
        parent::__construct($context);
        $this->_moduleManager = $context->getModuleManager();
        $this->_logger = $context->getLogger();
        $this->_request = $context->getRequest();
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->_httpHeader = $context->getHttpHeader();
        $this->_eventManager = $context->getEventManager();
        $this->_remoteAddress = $context->getRemoteAddress();
        $this->_cacheConfig = $context->getCacheConfig();
        $this->urlEncoder = $context->getUrlEncoder();
        $this->urlDecoder = $context->getUrlDecoder();
        $this->scopeConfig = $context->getScopeConfig();
        $this->encryptor = $encryptor;
        $this->smsService = $smsService;
        $this->storeManager = $storeManager;
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->customerSession = $customerSession;
    }
    public function isEnable()
    {
        $isEnable = $this->scopeConfig->isSetFlag(
            'smsnotification/general/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $isModuleEnable = $this->_moduleManager->isEnabled('DarshilTech_SmsNotification');
        if ($isEnable && $isModuleEnable) {
            return true;
        }
        return false;
    }
    public function getSmsProvider()
    {
        $isEnable = $this->isEnable();
        if ($isEnable) {
            $provider = $this->scopeConfig->getValue(
                'smsnotification/provider/provider_type',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            return $provider;
        }
        return null;
    }
    public function getTwilloCredential()
    {
        $result = [];

        $result['sid'] = $this->scopeConfig->getValue(
            'smsnotification/provider/twilio_sid',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $encryptedToken = $this->scopeConfig->getValue(
            'smsnotification/provider/twilio_token',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $result['token'] = $this->decrypt($encryptedToken);
        $result['number'] = $this->scopeConfig->getValue(
            'smsnotification/provider/twilio_number',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $result['service_id'] = $this->scopeConfig->getValue(
            'smsnotification/provider/twilio_service_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $result;
    }
    public function decrypt($value)
    {
        return $this->encryptor->decrypt($value);
    }
    public function formatNumberForTwillo(string $number): string
    {
        // Remove all non-digit characters except "+"
        $clean = preg_replace('/[^\d\+]/', '', $number);

        // Separate country code and national number
        if (preg_match('/^\+(\d{1,4})(\d{6,})$/', $clean, $matches)) {
            $countryCode = '+' . $matches[1];
            $nationalNumber = $matches[2];

            // Now split national number into readable chunks (e.g., 5-5, 4-4, etc.)
            $chunks = str_split($nationalNumber, 5);

            return $countryCode . ' ' . implode(' ', $chunks);
        }

        // If not a valid E.164 number, return as is
        return $number;
    }
    public function createLogs($event, $smslog)
    {
        $result = [
            'service_provider' => $this->getSmsProvider(),
            'event_type' => $event->getName() ?? 'Unknown Event',
            'recipient_number' => '',
            'message_body' => '',
            'status' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'error_message' => '',
            'is_error' => 0,
        ];

        try {
            if (is_array($smslog) && isset($smslog['error'])) {
                $result['message_body'] = $smslog['message'];
                $result['status'] = 'error';
                $result['error_message'] = $smslog['message'];
                $result['is_error'] = 1;
            } else {
                $result['recipient_number'] = $smslog->to;
                $result['message_body'] = $smslog->body;
                $result['status'] = $smslog->status;
                $result['created_at'] = date('Y-m-d H:i:s');
                $result['updated_at'] = date('Y-m-d H:i:s');
                $result['error_message'] = $smslog->errorMessage;
                $result['is_error'] = $smslog->errorCode ? 1 : 0;
            }
        } catch (\Exception $e) {
            $result['recipient_number'] = $smslog ? $smslog->to : 'Unknown Recipient';
            $result['message_body'] = $smslog ? $smslog->body : 'Unknown Message';
            $result['status'] = $smslog ? $smslog->status : 'Unknown Status';
            $result['created_at'] = $smslog ? date('Y-m-d H:i:s') : 'Unknown Date';
            $result['updated_at'] = $smslog ? date('Y-m-d H:i:s') : 'Unknown Date';
            $result['error_message'] = $e->getMessage();
            $result['is_error'] = 1;
        }

        return $result;
    }
    public function getSmsTemplate($eventName)
    {
        $template = $this->smsService->getCollection()
            ->addFieldToFilter('event', $eventName)
            ->getFirstItem();

        if ($template->getId()) {
            return $template->getTemplateContent();
        }

        return null;
    }
    public function getStoreEmail()
    {
        $storeEmail = $this->scopeConfig->getValue(
            'trans_email/ident_support/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeEmail;
    }

    public function getStorePhone()
    {
        $storePhone = $this->scopeConfig->getValue(
            'general/store_information/phone',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storePhone;
    }
    public function getStoreName()
    {
        return $this->storeManager->getStore()->getFrontendName();
    }
    public function getStoreUrl()
    {
        $storeUrl = $this->scopeConfig->getValue(
            'general/store_information/base_url',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeUrl;
    }
    public function getStoreHours()
    {
        return $this->scopeConfig->getValue('general/store_information/store_hours', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getStoreAddress()
    {
        $storeAddress = $this->scopeConfig->getValue(
            'general/store_information/address',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeAddress;
    }
    public function getStoreCity()
    {
        $storeCity = $this->scopeConfig->getValue(
            'general/store_information/city',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeCity;
    }
    public function getStoreCountry()
    {
        $storeCountry = $this->scopeConfig->getValue(
            'general/store_information/country_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeCountry;
    }
    public function getStoreRegion()
    {
        $storeRegion = $this->scopeConfig->getValue(
            'general/store_information/region_id',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeRegion;
    }
    public function getStoreZip()
    {
        $storeZip = $this->scopeConfig->getValue(
            'general/store_information/postcode',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $storeZip;
    }
    public function getCountryCode($countryCode)
    {

        $filePath = BP . '/app/code/DarshilTech/SmsNotification/etc/country_code.json';

        if (!file_exists($filePath)) {
            return '';
        }

        $json = file_get_contents($filePath);
        $data = json_decode($json, true);

        foreach ($data as $country) {
            if (strtoupper($country['code']) === strtoupper($countryCode)) {
                return $country['dial_code'];
            }
        }

        return '';
    }
    public function getDynamicVariables($entity, $event)
    {
        try {
            if ($entity == 'invoice') {
                $order = $event->getInvoice()->getOrder();
            } else if ($entity == 'shipment') {
                $order = $event->getShipment()->getOrder();
            } else if ($entity == 'creditmemo') {
                $order = $event->getCreditmemo()->getOrder();
            } else {
                $order = $event->getOrder();
            }

            if (!$order) {
                return [];
            }

            $customer_number = $order->getShippingAddress() ? $order->getShippingAddress()->getTelephone() : 'N/A';
            $customer_country_code = $order->getShippingAddress() ? $order->getShippingAddress()->getCountryId() : null;

            if ($customer_country_code) {
                $customer_number = $this->getCountryCode($customer_country_code) . $customer_number;
            }

            $variables = [
                '{{customer_name}}' => $order->getShippingAddress() ? $order->getShippingAddress()->getFirstname() . ' ' . $order->getShippingAddress()->getLastname() : 'N/A',
                '{{customer_email}}' => $order->getCustomerEmail() ?? 'N/A',
                '{{customer_number}}' => $customer_number ?? 'N/A',
                '{{order_id}}' => $order->getIncrementId() ?? 'N/A',
                '{{order_date}}' => $order->getCreatedAt() ? $this->dateTimeFormatter->formatDate($order->getCreatedAt(), \IntlDateFormatter::MEDIUM) : 'N/A',
                '{{order_total}}' => $order->getOrderCurrencyCode() && $order->getGrandTotal() ? $order->getOrderCurrencyCode() . ' ' . number_format($order->getGrandTotal(), 2) : 'N/A',
                '{{store_name}}' => $this->getStoreName() ?? 'N/A',
                '{{store_url}}' => $this->getStoreUrl() ?? 'N/A',
                '{{account_url}}' => $this->getStoreUrl() ? $this->getStoreUrl() . 'customer/account/' : 'N/A',
                '{{store_email}}' => $this->getStoreEmail() ?? 'N/A',
                '{{store_phone}}' => $this->getStorePhone() ?? 'N/A',
                '{{formattedBillingAddress}}' => $order->getBillingAddress() ? $this->formatAddress($order->getBillingAddress()) : 'N/A',
                '{{formattedShippingAddress}}' => $order->getShippingAddress() ? $this->formatAddress($order->getShippingAddress()) : 'N/A',
                '{{payment_method}}' => $order->getPayment() ? $order->getPayment()->getMethodInstance()->getTitle() : 'N/A',
                '{{shipping_method}}' => $order->getShippingMethod() ?? 'N/A',
                '{{store_hours}}' => $this->getStoreHours() ?? 'N/A',
            ];

            if ($entity == 'invoice') {
                $variables['{{invoice_id}}'] =  $event->getInvoice()->getIncrementId() ?? 'N/A';
                $variables['{{invoice_date}}'] = $event->getInvoice() ? $this->dateTimeFormatter->formatDate($order->getCreatedAt(), \IntlDateFormatter::MEDIUM) : 'N/A';
                $variables['{{invoice_total}}'] = $event->getInvoice() && $event->getInvoice()->getGrandTotal() ? $order->getOrderCurrencyCode() . ' ' . number_format($event->getInvoice()->getGrandTotal(), 2) : 'N/A';
            } else if ($entity == 'shipment') {
                $variables['{{shipment_id}}'] = $event->getShipment()->getIncrementId() ?? 'N/A';
                $variables['{{shipment_date}}'] = $event->getShipment()->getCreatedAt() ? $this->dateTimeFormatter->formatDate($order->getCreatedAt(), \IntlDateFormatter::MEDIUM) : 'N/A';
                $variables['{{shipment_total}}'] = $event->getShipment() && $event->getShipment()->getTotalWeight() ? $order->getOrderCurrencyCode() . ' ' . number_format($event->getShipment()->getTotalWeight(), 2) : 'N/A';
            } else if ($entity == 'creditmemo') {
                $variables['{{creditmemo_id}}'] = $event->getCreditmemo()->getIncrementId() ?? 'N/A';
                $variables['{{creditmemo_date}}'] = $event->getCreditmemo() ? $this->dateTimeFormatter->formatDate($order->getCreatedAt(), \IntlDateFormatter::MEDIUM) : 'N/A';
                $variables['{{creditmemo_total}}'] = $event->getCreditmemo() && $event->getCreditmemo()->getGrandTotal() ? $order->getOrderCurrencyCode() . ' ' . number_format($event->getCreditmemo()->getGrandTotal(), 2) : 'N/A';
            }

            return $variables;
        } catch (\Exception $e) {
            // add logger to print log
            return [];
        }
    }
    public function formatAddress($address): string
    {
        if (!$address) {
            return '';
        }

        return implode(', ', [
            $address->getStreetLine(1),
            $address->getCity(),
            $address->getRegion(),
            $address->getPostcode(),
            $address->getCountryId()
        ]);
    }
    public function isCustomerLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }
}