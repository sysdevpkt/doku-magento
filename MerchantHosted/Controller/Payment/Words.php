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
            $amount = number_format($this->session->getQuote()->getGrandTotal(), 2, '.', '');
            $currency = '360';
            $params = array(
                'amount' => $amount,
                'invoice' => $invoice_no,
                'currency' => $currency
            );
            $words = $this->doCreateWords($params);
            $getItems = $this->cart->getSectionData()['items'];
            $basket = '';

            foreach ($getItems as $getItem) {
                $basket .= $getItem['product_name'] .','. $getItem['product_price_value'] .','. $getItem['qty'] .','.
                    ($getItem['product_price_value'] * $getItem['qty']) .';';
            }

            $arr = array(
                'err' => false,
                'msg' => 'Create words success',
                'words' => $words,
                'invoice_no' => $invoice_no,
                'session_id' => $this->session->getSessionId(),
                'currency' => $currency,
                'form_type' => 'inline',
                'chain_merchant' => 'NA',
                'basket' => $basket,
                'amount' => $amount
            );

        }catch(\Exception $e){
            $arr = array('err' => true, 'msg' => 'Create words failed : '+ $e->getMessage());
        }

        $this->logger->info('===== Words Controller ===== End');

        echo json_encode($arr);

    }
}
