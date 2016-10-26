<?php
namespace Doku\MerchantHosted\Controller\Payment;

use Magento\Checkout\Model\Session;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;

class Words extends \Doku\MerchantHosted\Controller\Payment\Library
{

    protected $_session;

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config,
        Session $session
    )
    {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->_session = $session;
    }

    public function execute()
    {

        $this->logger->info('===== Words Controller ===== Start');
        $this->logger->info('amount : '. json_encode($this->_session->getQuote()->getBaseGrandTotal(), JSON_PRETTY_PRINT));
        $this->logger->info('quote id : '. json_encode($this->_session->getQuote(), JSON_PRETTY_PRINT));
//        $postData = json_decode($_POST['dataWords']);
//        $this->_logger->info('postdata : '. json_encode($postData, JSON_PRETTY_PRINT));
//
//        $params = array(
//            'amount' => $postData->amount,
//            'invoice' => $postData->trans_id,
//            'currency' => $postData->currency
//        );

        $this->logger->info('===== Words Controller ===== End');

        echo true;

    }
}
