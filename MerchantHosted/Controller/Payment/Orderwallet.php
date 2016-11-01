<?php

namespace Doku\MerchantHosted\Controller\Payment;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Checkout\Model\Session;

class Orderwallet extends \Doku\MerchantHosted\Controller\Payment\Library{

    protected $session;

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config,
        Session $session
    ) {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->session = $session;
    }

    public function execute(){

        $this->logger->info('===== Order Controller ===== Start');

        $postData = json_decode($_POST['dataResponse']);
        $postEmail = $_POST['dataEmail'];

        $this->logger->info('$postData : '. json_encode($postData, JSON_PRETTY_PRINT));

        $params = array(
            'amount' => $postData->req_amount,
            'invoice' => $postData->req_invoice_no,
            'currency' => $postData->req_currency,
            'token' => $postData->res_token_id,
            'pairing_code' => $postData->res_pairing_code
        );

        $words = $this->doCreateWords($params);
        $billingAddress = $this->session->getQuote()->getBillingAddress()->convertToArray();
        $customer = array(
            'name' => $billingAddress['firstname'] .' '. $billingAddress['lastname'],
            'data_phone' => $billingAddress['telephone'],
            'data_email' => $postEmail,
            'data_address' => $billingAddress['street'] .', '. $billingAddress['city'] .', '. $billingAddress['country_id']
        );

        try{

            $dataPayment = array(
                'req_mall_id' => $this->config->getMallId(),
                'req_chain_merchant' => "NA",
                'req_amount' => $postData->req_amount,
                'req_words' => $words,
                'req_purchase_amount' => $postData->req_amount,
                'req_trans_id_merchant' => $postData->req_invoice_no,
                'req_request_date_time' => date('YmdHis'),
                'req_currency' => $postData->req_currency,
                'req_purchase_currency' => $postData->req_currency,
                'req_session_id' => $this->session->getSessionId(),
                'req_name' => $customer['name'],
                'req_payment_channel' => $postData->req_payment_channel,
                'req_basket' => $postData->req_basket,
                'req_email' => $customer['data_email'],
                'req_token_id' => $postData->res_token_id,
                'req_mobile_phone' => $customer['data_phone'],
                'req_address' => $customer['data_address']
            );

        }catch(\Exception $e){
            $this->logger->info('data payment error = '. $e->getMessage());
        }

        $this->logger->info('data payment = '. json_encode($dataPayment, JSON_PRETTY_PRINT));

        $result = $this->doPayment($dataPayment);

        $this->logger->info('response payment = '. json_encode($result, JSON_PRETTY_PRINT));

        if($result->res_response_code == '0000'){

            echo json_encode(array('err' => false, 'res_response_msg' => 'Payment Success', 'res_response_code' => $result->res_response_code));

        }else{

            echo json_encode(array('err' => true, 'res_response_msg' => 'Payment Failed', 'res_response_code' => $result->res_response_code));

        }

    }

}
