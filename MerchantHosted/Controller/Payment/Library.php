<?php

namespace Doku\MerchantHosted\Controller\Payment;

abstract class Library extends \Magento\Framework\App\Action\Action{

    protected $logger;
    protected $config;
    protected $prePaymentUrl = 'https://staging.doku.com/api/payment/PrePayment';
    protected $paymentUrl = 'https://staging.doku.com/api/payment/paymentMip';
    protected $directPaymentUrl = 'https://staging.doku.com/api/payment/PaymentMIPDirect';
    protected $generateCodeUrl = 'https://staging.doku.com/api/payment/doGeneratePaymentCode';
    protected $redirectPaymentUrl = 'https://staging.doku.com/api/payment/doInitiatePayment';
    protected $captureUrl = 'https://staging.doku.com/api/payment/DoCapture';

    public function __construct(
        \Psr\Log\LoggerInterface $logger, //log injection
        \Magento\Framework\App\Action\Context $context,
        \Doku\MerchantHosted\Model\DokuConfigProvider $config

    ) {
        $this->logger = $logger;
        parent::__construct($context);
        $this->config = $config;
    }

    protected function doCreateWords($data){
        if(!empty($data['device_id']))
            if(!empty($data['pairing_code']))
                return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'] . $data['device_id']);
            else
                return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency'] . $data['device_id']);
        else if(!empty($data['pairing_code']))
            return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code']);
        else if(!empty($data['currency']))
            return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency']);
        else
            return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice']);
    }
    
}