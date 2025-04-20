# module-smsnotification

# DarshilTech_SmsNotification

A Magento 2 module to send SMS notifications using [Twilio](https://www.twilio.com/). Automatically installs the Twilio SDK when this module is required via Composer.

## 🚀 Features

- Send SMS notifications via Twilio (e.g., on new order)
- Easy installation via Composer
- No need to install `twilio/sdk` separately

## 🧱 Requirements

- Magento 2.4.x or later
- PHP >= 7.4
- Twilio Account and API credentials

## 📦 Installation

### Install via Composer (Packagist or GitHub VCS)

```bash
composer require darshiltech/module-smsnotification
bin/magento module:enable DarshilTech_SmsNotification
bin/magento setup:upgrade
