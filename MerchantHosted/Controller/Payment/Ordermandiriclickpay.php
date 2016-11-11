<?php

namespace Doku\MerchantHosted\Controller\Payment;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Checkout\Model\Session;
use \Doku\MerchantHosted\Controller\Payment\Library;
use Magento\Checkout\CustomerData\Cart;
use Magento\Framework\App\ResourceConnection;

class Ordermandiriclickpay extends Library{

    protected $session;
    protected $cart;
    protected $resourceConnection;

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config,
        Session $session,
        Cart $cart,
        ResourceConnection $resourceConnection
    ) {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->session = $session;
        $this->cart = $cart;
        $this->resourceConnection = $resourceConnection;
    }

    public function execute(){

        $this->logger->info('===== Ordermandiriclickpay Controller ===== Start');

        try{

            $this->logger->info('post : '. json_encode($_POST, JSON_PRETTY_PRINT));

            $postData = json_decode($_POST['dataResponse']);
            $invoice_no = 'mage2'. $this->config->getMallId() . str_pad($this->session->getQuoteId(), 9, '0', STR_PAD_LEFT);
            $amount = number_format($this->session->getQuote()->getGrandTotal(), 2, '.', '');
            $currency = '360';

            $params = array(
                'amount' => $amount,
                'invoice' => $invoice_no,
                'currency' => $currency
            );

            $cc = str_replace(" - ", "", $_POST['cc_number']);
            $words = $this->doCreateWords($params);
            $billingAddress = $this->session->getQuote()->getBillingAddress()->convertToArray();
            $customer = array(
                'name' => $billingAddress['firstname'] .' '. $billingAddress['lastname'],
                'data_phone' => substr($billingAddress['telephone'], 0, 12),
                'data_email' => $postData->req_email,
                'data_address' => $billingAddress['street'] .', '. $billingAddress['city'] .', '. $billingAddress['country_id']
            );

            $getItems = $this->cart->getSectionData()['items'];
            $basket = '';

            foreach ($getItems as $getItem) {
                $basket .= $getItem['product_name'] .','. $getItem['product_price_value'] .','. $getItem['qty'] .','.
                    ($getItem['product_price_value'] * $getItem['qty']) .';';
            }

            $dataPayment = array(
                'req_mall_id' => $this->config->getMallId(),
                'req_chain_merchant' => 'NA',
                'req_amount' => $amount,
                'req_words' => $words,
                'req_purchase_amount' => $amount,
                'req_trans_id_merchant' => $invoice_no,
                'req_request_date_time' => date('YmdHis'),
                'req_currency' => $currency,
                'req_purchase_currency' => $currency,
                'req_session_id' => $this->session->getSessionId(),
                'req_name' => $customer['name'],
                'req_payment_channel' => $postData['req_payment_channel'],
                'req_email' => $customer['data_email'],
                'req_card_number' => $cc,
                'req_basket' => $basket,
                'req_challenge_code_1' => $postData['challenge_code1'],
                'req_challenge_code_2' => $postData['challenge_code2'],
                'req_challenge_code_3' => $postData['challenge_code3'],
                'req_response_token' => $postData['response_token'],
                'req_mobile_phone' => $customer['data_phone'],
                'req_address' => $customer['data_address']
            );

            $result = $this->doDirectPayment($dataPayment);

            $this->logger->info('response payment = '. json_encode($result, JSON_PRETTY_PRINT));
            $this->logger->info('===== Ordermandiriclickpay Controller ===== End');

            if($result->res_response_code == '0000'){

                $this->logger->info('===== Ordermandiriclickpay Controller ===== Saving data...');
                $this->resourceConnection->getConnection()->insert('doku_orders',
                    [
                        'quote_id' => $this->session->getQuoteId(),
                        'store_id' => $this->session->getQuote()->getStoreId(),
                        'invoice_no' => $invoice_no,
                        'payment_channel_id' => $postData->req_payment_channel,
                        'order_status' => $postData->res_response_msg
                    ]);

                $this->logger->info('===== Ordermandiriclickpay Controller ===== Saving complete');
                $this->logger->info('===== Ordermandiriclickpay Controller ===== End');

                echo json_encode(array('err' => false, 'res_response_msg' => 'Payment Success', 'res_response_code' => $result->res_response_code));

            }else{

                echo json_encode(array('err' => true, 'res_response_msg' => 'Payment Failed', 'res_response_code' => $result->res_response_code));

            }

        }catch(\Exception $e){

            $this->logger->info('===== Ordermandiriclickpay Controller ===== Payment error : '. $e->getMessage());
            $this->logger->info('===== Ordermandiriclickpay Controller ===== End');

            echo json_encode(array('err' => true, 'res_response_msg' => $e->getMessage(), 'res_response_code' => '0099'));

        }

    }

}
