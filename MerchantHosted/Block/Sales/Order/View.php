<?php

namespace Doku\MerchantHosted\Block;

use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Helper\Data as PaymentData;
use Psr\Log\LoggerInterface as Logger;

class View extends \Magento\Framework\View\Element\Template{

    protected $paymentData;
    private $logger;

    public function __construct(
        Context $context,
        array $data = [],
        PaymentData $paymentData,
        Logger $logger
    ){
        parent::__construct(
           $context, $data
        );

        $this->paymentData = $paymentData;
        $this->logger = $logger;
    }

    public function getOrder(){

        $this->logger->info('order'. json_encode($this->getParentBlock()->getOrder(), JSON_PRETTY_PRINT));
        return true;

    }

}