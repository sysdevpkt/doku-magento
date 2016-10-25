<?php

namespace Doku\MerchantHosted\Controller\Payment;

use Doku\MerchantHosted\Model\Oco;

class Order extends \Magento\Framework\App\Action\Action{

	protected $_logger;

    public function __construct(
        \Psr\Log\LoggerInterface $logger, //log injection
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->_logger = $logger;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }
    
}