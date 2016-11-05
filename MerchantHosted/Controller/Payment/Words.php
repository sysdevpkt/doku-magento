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

            $invoice_no = 'mage2'. $this->config->getMallId() . $this->session->getQuote()->getReservedOrderId();
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
                'req_response_msg' => 'Create words success',
                'req_words' => $words,
                'req_invoice_no' => $invoice_no,
                'req_session_id' => $this->session->getSessionId(),
                'req_currency' => $currency,
                'req_form_type' => 'inline',
                'req_chain_merchant' => 'NA',
                'req_basket' => $basket,
                'req_amount' => $amount
            );

        }catch(\Exception $e){
            $arr = array('err' => true, 'msg' => 'Create words failed : '+ $e->getMessage());
        }

        $this->logger->info('===== Words Controller ===== End');

        echo json_encode($arr);

    }
}
