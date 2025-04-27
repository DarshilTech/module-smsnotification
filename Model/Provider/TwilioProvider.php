<?php
namespace DarshilTech\SmsNotification\Model\Provider;

use DarshilTech\SmsNotification\Helper\Data;
use Twilio\Rest\Client;

class TwilioProvider
{
    protected $helper;
    protected $twilioClient;
    protected $accountSid;
    protected $authToken;
    protected $fromNumber;
    protected $serviceid;

    public function __construct(Data $helper)
    {
        $this->helper = $helper;
        $this->init();
    }

    /**
     * Initialize Twilio credentials and client
     */
    public function init(): void
    {
        $credentials = $this->helper->getTwilloCredential();
        $this->accountSid = $credentials['sid'] ?? null;
        $this->authToken = $credentials['token'] ?? null;
        $this->fromNumber = $credentials['number'] ?? null;
        $this->serviceid = $credentials['service_id'] ?? null;
        if ($this->accountSid && $this->authToken) {
            $this->twilioClient = new Client($this->accountSid, $this->authToken);

        }
    }

    /**
     * Send SMS using Twilio
     *
     * @param string $to Recipient mobile number (e.g., '+919999999999')
     * @param string $message Message text
     * @return bool|string SID if success, false if failed
     */
    public function sendSms(string $to, string $message)
    {
        try {
            if (!$this->twilioClient) {
                throw new \Exception('Twilio client not properly initialized.');
            }

            $params = [
                'body' => $message,
                'to' => $to,
            ];

            if ($this->serviceid) {
                $params['messagingServiceSid'] = $this->serviceid;
            } elseif ($this->fromNumber) {
                $params['from'] = $this->fromNumber;
            } else {
                throw new \Exception('Neither messaging service ID nor from number is set.');
            }

            $result = $this->twilioClient->messages->create($to, $params);
            return $result;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];

        }
    }
    public function getSendotp($otp_code, $mobile_number)
    {
        try {
            if (!$this->twilioClient) {
                throw new \Exception('Twilio client not properly initialized.');
            }
            $params = [
                'body' => $otp_code,
                'to' => $mobile_number,
            ];
            if ($this->serviceid) {
                $params['messagingServiceSid'] = $this->serviceid;
            } elseif ($this->fromNumber) {
                $params['from'] = $this->fromNumber;
            } else {
                throw new \Exception('Neither messaging service ID nor from number is set.');
            }
            $result = $this->twilioClient->messages->create($mobile_number, $params);
            return $result;
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

}
