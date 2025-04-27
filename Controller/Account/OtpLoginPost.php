<?php


namespace DarshilTech\SmsNotification\Controller\Account;

use Magento\Framework\Session\SessionManagerInterface;
use DarshilTech\SmsNotification\Model\Provider\TwilioProvider;
use DarshilTech\SmsNotification\Model\SmsLog;
/**
 * Class OtpLoginPost
 * @package DarshilTech\SmsNotification\Controller\Account
 */
class OtpLoginPost extends \Magento\Framework\App\Action\Action
{

    /**
     * OtpLoginPost constructor
     * @param \Magento\Framework\App\Action\Context                $context            [description]
     * @param \Magento\Framework\Session\SessionManagerInterface   $session            [description]
     * @param \DarshilTech\SmsNotification\Helper\Data                        $helper             [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory  [description]
     * @param \Magento\Customer\Model\ResourceModel\Customer\Collection $collection    [description]    
     */
    protected $smsProvider;
    protected $smsLog;
    protected $helper;
    protected $collection;
    protected $resultJsonFactory;
    protected $_sessionManager;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManagerInterface $session,
        \DarshilTech\SmsNotification\Helper\Data $helper,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        SmsLog $smsLog,
        \Magento\Customer\Model\ResourceModel\Customer\Collection $collection
    ) {
        $this->helper = $helper;
        $this->smsLog = $smsLog;
        $this->collection = $collection;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_sessionManager = $session;
        $provider = $this->helper->getSmsProvider();
        if ($provider === 'twilio') {
            $this->smsProvider = \Magento\Framework\App\ObjectManager::getInstance()->create(TwilioProvider::class);
        }
        parent::__construct($context);
    }

    /**
     * @return PageFactory
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (!isset($params['email'])) {
            $collection = $this->collection->addAttributeToSelect('*')
                ->addAttributeToFilter('customer_mobile_number', $params['customer_mobile_number'])
                ->load()->getData();
            if (empty($collection)) {
                $response = [
                    'errors' => true,
                    'message' => __("Mobile Number Not Registered")
                ];
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($response);
            }
        }
        // set session
        $session = $this->_sessionManager;
        $session->setUserFormData($params);

        //update status
        try {
            $this->helper->setUpdateotpstatus($params['customer_mobile_number']);

            //otp
            $otp_code = $this->helper->getOtpcode();
            $isEnable = $this->helper->isEnable();
            //sms
            if ($isEnable && $this->smsProvider) {
                $template = $this->helper->getSmsTemplate('login_otp');
                if ($template) {
                    $variables = [
                        '{{OTP}}' => $otp_code,
                        '{{store_name}}' => $this->helper->getStoreName(),
                        '{{validity_time}}' => $this->helper->getExpiretime(),
                    ];
                    $message = str_replace(array_keys($variables), array_values($variables), $template);
                    if (!$params['customer_mobile_number']) {
                        return;
                    }
                }
                $result = $this->smsProvider->getSendotp($message, $params['customer_mobile_number']);
                $resultLog = $this->helper->createLogs('login_otp', $result);
                $smsLog = $this->smsLog;
                $smsLog->setData($resultLog);
                $smsLog->save();
            } else {
                $response = [
                    'errors' => true,
                    'message' => __('SMS Provider Not Found')
                ];
                $resultJson = $this->resultJsonFactory->create();
                return $resultJson->setData($response);
            }

            //save data
            $otp = base64_encode($otp_code);
            $this->helper->setOtpdata($otp, $params['customer_mobile_number']);

            $response = [
                'errors' => false,
                'message' => __('OTP send to your Mobile Number')
            ];
        } catch (\Exception $e) {
            $response = [
                'errors' => true,
                'message' => $e->getMessage()
            ];
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }
}
