<?php
namespace Doku\MerchantHosted\Controller\Payment;

use Magento\Checkout\Model\Session;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Checkout\CustomerData\Cart;

class Words extends \Doku\MerchantHosted\Controller\Payment\Library
{

    protected $session;
    protected $cart;

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config,
        Session $session,
        Cart $cart
    )
    {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->session = $session;
        $this->cart = $cart;
    }

    public function execute()
    {

        $this->logger->info('===== Words Controller ===== Start');

        try{

            $invoice_no = 'mage2_'. $this->config->getMallId() . $this->session->getQuoteId() . $_GET['_'];
            $amount = number_format($this->session->getQuote()->getBaseGrandTotal(), 2);
            $currency = '360';
            $params = array(
                'amount' => $amount,
                'invoice' => $invoice_no,
                'currency' => $currency
            );

            $this->logger->info('params : '. json_encode($params, JSON_PRETTY_PRINT));
            $this->logger->info('session : '. json_encode($this->session->getQuote()->convertToArray(), JSON_PRETTY_PRINT));
            $this->logger->info('section : '. json_encode($this->cart->getSectionData(), JSON_PRETTY_PRINT));

            $words = $this->doCreateWords($params);
            $arr = array(
                'err' => false,
                'msg' => 'Create words success',
                'words' => $words,
                'invoice_no' => $invoice_no,
                'session_id' => $this->session->getSessionId(),
                'currency' => $currency,
                'payment_channel' => '15',
                'form_type' => 'inline',
                'chain_merchant' => 'NA'
            );

        }catch(\Exception $e){
            $arr = array('err' => true, 'msg' => 'Create words failed : '+ $e->getMessage());
        }

        echo json_encode($arr);

    }
}
