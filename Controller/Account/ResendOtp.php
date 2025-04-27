<?php



namespace DarshilTech\SmsNotification\Controller\Account;

use Magento\Framework\Session\SessionManagerInterface;
use DarshilTech\SmsNotification\Model\Provider\TwilioProvider;
use DarshilTech\SmsNotification\Model\SmsLog;
/**
 * Class ResendOtp
 * @package DarshilTech\SmsNotification\Controller\Account
 */
class ResendOtp extends \Magento\Framework\App\Action\Action
{
    /**
     * ResendOtp constructor
     * @param \Magento\Framework\App\Action\Context                $context        [description]
     * @param \Magento\Framework\Session\SessionManagerInterface   $session        [description]
     * @param \DarshilTech\SmsNotification\Helper\Data                        $helper         [description]
     * @param \Magento\Framework\Controller\Result\JsonFactory     $resultJsonFactory  [description]
     */
    protected $smsProvider;
    protected $smsLog;
    protected $helper;
    protected $_sessionManager;
    protected $resultJsonFactory;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        SessionManagerInterface $session,
        \DarshilTech\SmsNotification\Helper\Data $helper,
        SmsLog $smsLog,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->helper = $helper;
        $this->smsLog = $smsLog;
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
        //get session
        $sessiondata = $this->_sessionManager->getUserFormData();

        try {
            //update status
            $this->helper->setUpdateotpstatus($sessiondata['customer_mobile_number']);

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
                    if (!$sessiondata['customer_mobile_number']) {
                        return;
                    }
                }
                $result = $this->smsProvider->getSendotp($message, $sessiondata['customer_mobile_number']);
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
            $this->helper->setOtpdata($otp, $sessiondata['customer_mobile_number']);

            $response = [
                'errors' => false,
                'message' => __('OTP Resend Successfully.')
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
