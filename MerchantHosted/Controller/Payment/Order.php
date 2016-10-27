<?php

namespace Doku\MerchantHosted\Controller\Payment;

use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Checkout\Model\Session;

class Order extends \Doku\MerchantHosted\Controller\Payment\Library{

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
        $postObj = json_decode($_POST['dataObj']);

        $this->logger->info('get : '. json_encode($_GET, JSON_PRETTY_PRINT));
        $this->logger->info('postdata : '. json_encode($postData, JSON_PRETTY_PRINT));
        $this->logger->info('postobj : '. json_encode($postObj, JSON_PRETTY_PRINT));

        $params = array(
            'amount' => $postData->res_amount,
            'invoice' => $postData->res_invoice_no,
            'currency' => $postData->res_currency,
            'token' => $postData->res_token_id,
            'pairing_code' => $postData->res_pairing_code
        );

        $words = $this->doCreateWords($params);
        $billingAddress = $this->session->getQuote()->getBillingAddress()->convertToArray();
        $shippingAddress = $this->session->getQuote()->getShippingAddress()->getCustomerAddress()->get;

        $this->logger->info('billing : '. json_encode($billingAddress, JSON_PRETTY_PRINT));
        $this->logger->info('shipping : '. json_encode($shippingAddress, JSON_PRETTY_PRINT));

//        $this->logger->info('$words : '. json_encode($words, JSON_PRETTY_PRINT));
//
//        $basket = "adidas, 10000.00, 1, 10000.00;";
//
//        $this->logger->info('basket : '. json_encode($basket, JSON_PRETTY_PRINT));
//
        $customer = array(
            'name' => $billingAddress['firstname'] .' '. $billingAddress['lastname'],
            'data_phone' => $billingAddress['telephone'],
            'data_email' => 'test@test.com',
            'data_address' => $billingAddress['street'] .', '. $billingAddress['city'] .', '. $billingAddress['country_id']
        );
//
//        $this->logger->info('$customer : '. json_encode($customer, JSON_PRETTY_PRINT));
//
//        $data = array(
//            'req_token_id' => $postData->res_token_id,
//            'req_pairing_code' => $postData->res_pairing_code,
//            'req_bin_filter' => array("411111", "548117", "433???6", "41*3"),
//            'req_customer' => $customer,
//            'req_basket' => $basket,
//            'req_words' => $words
//        );

//        echo json_encode(array('err' => false, 'msg' => 'Payment Success', 'res_response_code' => '0000'));


//        $ch = curl_init( 'https://staging.doku.com/api/payment/PrePayment' );
//        curl_setopt( $ch, CURLOPT_POST, 1);
//        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
//        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
//        curl_setopt( $ch, CURLOPT_HEADER, 0);
//        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
//
//        $responseJson = curl_exec( $ch );
//
//        curl_close($ch);
//
//        $responsePrePayment = json_decode($responseJson);
//
//        $this->logger->info('response prepayment = '. json_encode($responsePrePayment, JSON_PRETTY_PRINT));

//        if($responsePrePayment->res_response_code == '0000'){
//
//            $dataPayment = array(
//                'req_mall_id' => 2074,
//                'req_chain_merchant' => "NA",
//                'req_amount' => '10000.00',
//                'req_words' => $words,
//                'req_purchase_amount' => '10000.00',
//                'req_trans_id_merchant' => $postData->res_invoice_no,
//                'req_request_date_time' => date('YmdHis'),
//                'req_currency' => '360',
//                'req_purchase_currency' => '360',
//                'req_session_id' => sha1(date('YmdHis')),
//                'req_name' => $customer['name'],
//                'req_payment_channel' => 15,
//                'req_basket' => $basket,
//                'req_email' => $customer['data_email'],
//                'req_token_id' => $postData->res_token_id,
//                'req_mobile_phone' => $customer['data_phone'],
//                'req_address' => $customer['data_address']
//            );
//
//
//            $ch = curl_init( "https://staging.doku.com/api/payment/paymentMip" );
//            curl_setopt( $ch, CURLOPT_POST, 1);
//            curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($dataPayment));
//            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
//            curl_setopt( $ch, CURLOPT_HEADER, 0);
//            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
//
//            $responseJsonPayment = curl_exec( $ch );
//
//            curl_close($ch);
//
//            if(is_string($responseJsonPayment)){
//                $responsePayment = json_decode($responseJsonPayment);
//            }else{
//                $responsePayment = $responseJsonPayment;
//            }
//
//            $this->logger->info('response payment = '. json_encode($responsePayment, JSON_PRETTY_PRINT));
//
//            if($responsePayment->res_response_code == '0000'){
//
//                echo json_encode(array('err' => false, 'res_response_msg' => 'Payment Success', 'res_response_code' => $responsePayment->res_response_code));
//
//            }else{
//
//                echo json_encode(array('err' => true, 'res_response_msg' => 'Payment Failed', 'res_response_code' => $responsePayment->res_response_code));
//
//            }
//
//
//        }else{
//            echo json_encode(array('err' => true, 'res_response_msg' => 'Prepayment Failed', 'res_response_code' => $responsePrePayment->res_response_code));
//        }

    }

}
