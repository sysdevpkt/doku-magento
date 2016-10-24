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

        $this->_logger->info('post : '. json_encode(json_decode($_POST['dataResponse']), JSON_PRETTY_PRINT));

        $words = sha1('10000.00' . '2074' . 'D0Ku123m3Rc' . 'invoice_1477040126' . '360');
        $this->_logger->addInfo('words = '. $words);

        $basket = "adidas, 10000.00, 1, 10000.00;";

        $customer = array(
            'name' => 'TEST NAME',
            'data_phone' => '08121111111',
            'data_email' => 'test@test.com',
            'data_address' => 'bojong gede #1 08/01'
        );

        $data = array(
            'req_token_id' => $_POST['doku-token'],
            'req_pairing_code' => $_POST['doku-pairing-code'],
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

        $this->_logger->info('response json = '. json_encode($responseJson, JSON_PRETTY_PRINT));

//        $responsePrePayment = json_decode($responseJson);
//
//        if($responsePrePayment->res_response_code == '0000'){
//
//            $dataPayment = array(
//                'req_mall_id' => 2074,
//                'req_chain_merchant' => "NA",
//                'req_amount' => '10000.00',
//                'req_words' => $words,
//                'req_purchase_amount' => '10000.00',
//                'req_trans_id_merchant' => 'invoice_1477040126',
//                'req_request_date_time' => date('YmdHis'),
//                'req_currency' => '360',
//                'req_purchase_currency' => '360',
//                'req_session_id' => sha1(date('YmdHis')),
//                'req_name' => $customer['name'],
//                'req_payment_channel' => 15,
//                'req_basket' => $basket,
//                'req_email' => $customer['data_email'],
//                'req_token_id' => $_POST['doku-token'],
//                'req_mobile_phone' => $customer['data_phone'],
//                'req_address' => $customer['data_address']
//            );
//
//        }else{
//            return 'prepayment failed';
//        }

    }

}
