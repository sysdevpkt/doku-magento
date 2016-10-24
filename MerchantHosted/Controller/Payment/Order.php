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

    public function execute(){

        $postData = json_decode($_POST['dataResponse']);

        $this->_logger->info('postdata : '. json_encode($postData, JSON_PRETTY_PRINT));

        $words = sha1('10000.00' . '2074' . 'D0Ku123m3Rc' . $postData->res_invoice_no . '360' . $postData->res_token_id . $postData->res_pairing_code);

        $this->_logger->info('$words : '. json_encode($words, JSON_PRETTY_PRINT));

        $basket = "adidas, 10000.00, 1, 10000.00;";

        $this->_logger->info('basket : '. json_encode($basket, JSON_PRETTY_PRINT));

        $customer = array(
            'name' => 'TEST NAME',
            'data_phone' => '08121111111',
            'data_email' => 'test@test.com',
            'data_address' => 'bojong gede #1 08/01'
        );

        $this->_logger->info('$customer : '. json_encode($customer, JSON_PRETTY_PRINT));

        $data = array(
            'req_token_id' => $postData->res_token_id,
            'req_pairing_code' => $postData->res_pairing_code,
            'req_bin_filter' => array("411111", "548117", "433???6", "41*3"),
            'req_customer' => $customer,
            'req_basket' => $basket,
            'req_words' => $words
        );

        $ch = curl_init( 'https://staging.doku.com/api/payment/PrePayment' );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $responseJson = curl_exec( $ch );

        curl_close($ch);

        $responsePrePayment = json_decode($responseJson);

        $this->_logger->info('response prepayment = '. json_encode($responsePrePayment, JSON_PRETTY_PRINT));

        if($responsePrePayment->res_response_code == '0000'){

            $dataPayment = array(
                'req_mall_id' => 2074,
                'req_chain_merchant' => "NA",
                'req_amount' => '10000.00',
                'req_words' => $words,
                'req_purchase_amount' => '10000.00',
                'req_trans_id_merchant' => $postData->res_invoice_no,
                'req_request_date_time' => date('YmdHis'),
                'req_currency' => '360',
                'req_purchase_currency' => '360',
                'req_session_id' => sha1(date('YmdHis')),
                'req_name' => $customer['name'],
                'req_payment_channel' => 15,
                'req_basket' => $basket,
                'req_email' => $customer['data_email'],
                'req_token_id' => $postData->res_token_id,
                'req_mobile_phone' => $customer['data_phone'],
                'req_address' => $customer['data_address']
            );


            $ch = curl_init( "https://staging.doku.com/api/payment/paymentMip" );
            curl_setopt( $ch, CURLOPT_POST, 1);
            curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($dataPayment));
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt( $ch, CURLOPT_HEADER, 0);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

            $responseJsonPayment = curl_exec( $ch );

            curl_close($ch);

            if(is_string($responseJsonPayment)){
                $responsePayment = json_decode($responseJsonPayment);
            }else{
                $responsePayment = $responseJsonPayment;
            }

            $this->_logger->info('response payment = '. json_encode($responsePayment, JSON_PRETTY_PRINT));


        }else{
            return json_encode(array('res_response_msg' => 'Prepayment Failed', 'res_response_code' => $responsePrePayment->res_response_code));
        }

    }

}
