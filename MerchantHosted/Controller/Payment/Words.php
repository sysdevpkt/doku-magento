<?php
namespace Doku\MerchantHosted\Controller\Payment;

use Doku\MerchantHosted\Model\Oco;
use Magento\Checkout\Model\Session;

class Words extends \Doku\MerchantHosted\Controller\Payment\Library
{
    public function __construct(
        \Psr\Log\LoggerInterface $logger, //log injection
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Session $session
    )
    {
        parent::__construct(
            $logger,
            $context,
            $resultPageFactory,
            $scopeConfig
        );

        $this->_session = $session;
    }

    public function execute()
    {

        $this->_logger->info('===== Words Controller ===== Start');
        $this->_logger->info('amount : '. json_encode($this->_session->getQuote()->getBaseGrandTotal(), JSON_PRETTY_PRINT));
        $this->_logger->info('quote id : '. json_encode($this->_session->getQuoteId(), JSON_PRETTY_PRINT));
        $this->_logger->info('session id : '. json_encode($this->_session->getSessionId(), JSON_PRETTY_PRINT));
        $this->_logger->info('getOrigOrderId : '. json_encode($this->_session->getQuote()->getOrigOrderId(), JSON_PRETTY_PRINT));
        $this->_logger->info('getReservedOrderId : '. json_encode($this->_session->getQuote()->getReservedOrderId(), JSON_PRETTY_PRINT));
//        $postData = json_decode($_POST['dataWords']);
//        $this->_logger->info('postdata : '. json_encode($postData, JSON_PRETTY_PRINT));
//
//        $params = array(
//            'amount' => $postData->amount,
//            'invoice' => $postData->trans_id,
//            'currency' => $postData->currency
//        );

        $this->_logger->info('===== Words Controller ===== End');

        echo true;

    }
}
