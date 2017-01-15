<?php

namespace Doku\MerchantHosted\Block\Sales\Order;

use Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Registry;
use Psr\Log\LoggerInterface as Logger;

class View extends \Magento\Framework\View\Element\Template{

    private $registry;
    private $logger;

    public function __construct(
        Context $context,
        array $data = [],
        Registry $registry,
        Logger $logger
    ){
        parent::__construct(
           $context, $data
        );

        $this->registry = $registry;
        $this->logger = $logger;
    }

    private function getOrder()
    {
        return $this->registry->registry('sales_order');
    }

    public function getOrderData(){

        $this->logger->info('order'. json_encode($this->getOrder()->convertToArray(), JSON_PRETTY_PRINT));
        return true;

    }

}