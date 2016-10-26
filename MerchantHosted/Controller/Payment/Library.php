<?php

namespace Doku\MerchantHosted\Controller\Payment;

use Doku\MerchantHosted\Model\Oco;

abstract class Library extends \Magento\Framework\App\Action\Action{

    protected $_logger;
    protected $_scopeConfig;
    protected $prePaymentUrl = 'https://staging.doku.com/api/payment/PrePayment';
    protected $paymentUrl = 'https://staging.doku.com/api/payment/paymentMip';
    protected $directPaymentUrl = 'https://staging.doku.com/api/payment/PaymentMIPDirect';
    protected $generateCodeUrl = 'https://staging.doku.com/api/payment/doGeneratePaymentCode';
    protected $redirectPaymentUrl = 'https://staging.doku.com/api/payment/doInitiatePayment';
    protected $captureUrl = 'https://staging.doku.com/api/payment/DoCapture';

    public function __construct(
        \Psr\Log\LoggerInterface $logger, //log injection
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory

    ) {
        $this->_logger = $logger;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    protected function getMallId(){
        return $this->_scopeConfig->getValue('payment/oco/mall_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function getSharedKey(){
        return $this->_scopeConfig->getValue('payment/oco/shared_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    protected function doCreateWords($data){
        if(!empty($data['device_id']))
            if(!empty($data['pairing_code']))
                return sha1($data['amount'] . $this->getMallId() . $this->getSharedKey() . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'] . $data['device_id']);
            else
                return sha1($data['amount'] . $this->getMallId() . $this->getSharedKey() . $data['invoice'] . $data['currency'] . $data['device_id']);
        else if(!empty($data['pairing_code']))
            return sha1($data['amount'] . $this->getMallId() . $this->getSharedKey() . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code']);
        else if(!empty($data['currency']))
            return sha1($data['amount'] . $this->getMallId() . $this->getSharedKey() . $data['invoice'] . $data['currency']);
        else
            return sha1($data['amount'] . $this->getMallId() . $this->getSharedKey() . $data['invoice']);
    }
    
}