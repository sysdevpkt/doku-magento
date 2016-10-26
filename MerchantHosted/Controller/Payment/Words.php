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
        $this->logger->info('post : '. json_encode($_POST, JSON_PRETTY_PRINT));

        $params = array(
            'amount' => number_format($this->_session->getQuote()->getBaseGrandTotal(), 2),
            'invoice' => 'mage2_'. $_POST['_'],
            'currency' => '360'
        );

        $words = $this->doCreateWords($params);

        $this->logger->info('===== Words Controller ===== End');

        echo $words;

    }
}
