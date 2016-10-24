<?php

namespace Doku\MerchantHosted\Controller\Payment;

use Doku\MerchantHosted\Model\Oco;

class Order{

    protected $_logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger //log injection
    ) {
        $this->_logger = $logger;
    }

    public function execute(){

        $this->_logger->addInfo('execute');

    }

}
