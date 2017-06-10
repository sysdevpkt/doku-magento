<?php

namespace Doku\MerchantHosted\Controller\Payment;

use Doku\MerchantHosted\Model\DokuConfigProvider;
use \Magento\Framework\App\Action\Context;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Action;

abstract class Library extends Action{

    protected $config;
    protected $prePaymentUrl;
    protected $paymentUrl;
    protected $directPaymentUrl;
    protected $generateCodeUrl;
    protected $redirectPaymentUrl;
    protected $captureUrl;
/*    const prePaymentUrl = 'https://staging.doku.com/api/payment/PrePayment';
    const paymentUrl = 'https://staging.doku.com/api/payment/paymentMip';
    const directPaymentUrl = 'https://staging.doku.com/api/payment/PaymentMIPDirect';
    const generateCodeUrl = 'https://staging.doku.com/api/payment/doGeneratePaymentCode';
    const redirectPaymentUrl = 'https://staging.doku.com/api/payment/doInitiatePayment';
    const captureUrl = 'https://staging.doku.com/api/payment/DoCapture'; */

    public function __construct(
        LoggerInterface $logger, //log injection
        Context $context,
        DokuConfigProvider $config

    ) {
        $this->logger = $logger;
        parent::__construct($context);
        $this->config = $config;
        if($this->config->getEnvironment() == 'Production') {
            $this->prePaymentUrl = 'https://pay.doku.com/api/payment/PrePayment';
            $this->paymentUrl = 'https://pay.doku.com/api/payment/paymentMip';
            $this->directPaymentUrl = 'https://pay.doku.com/api/payment/PaymentMIPDirect';
            $this->generateCodeUrl = 'https://pay.doku.com/api/payment/doGeneratePaymentCode';
            $this->redirectPaymentUrl = 'https://pay.doku.com/api/payment/doInitiatePayment';
            $this->captureUrl = 'https://pay.doku.com/api/payment/DoCapture';
        } else {
            $this->prePaymentUrl = 'https://staging.doku.com/api/payment/PrePayment';
            $this->paymentUrl = 'https://staging.doku.com/api/payment/paymentMip';
            $this->directPaymentUrl = 'https://staging.doku.com/api/payment/PaymentMIPDirect';
            $this->generateCodeUrl = 'https://staging.doku.com/api/payment/doGeneratePaymentCode';
            $this->redirectPaymentUrl = 'https://staging.doku.com/api/payment/doInitiatePayment';
            $this->captureUrl = 'https://staging.doku.com/api/payment/DoCapture';
        }
    }

    protected function formatBasket($data){
        $parseBasket = '';
        if(is_array($data))
            foreach($data as $basket)
                $parseBasket .= $basket['name'] .','. $basket['amount'] .','. $basket['quantity'] .','. $basket['subtotal'] .';';
        else if(is_object($data))
            foreach($data as $basket)
                $parseBasket .= $basket->name .','. $basket->amount .','. $basket->quantity .','. $basket->subtotal .';';
        else
            $parseBasket = $data;
        return $parseBasket;
    }

    protected function doCreateWords($data){
        if(!empty($data['device_id']))
            if(!empty($data['pairing_code']))
                return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code'] . $data['device_id']);
            else
                return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency'] . $data['device_id']);
        else if(!empty($data['pairing_code']))
            return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency'] . $data['token'] . $data['pairing_code']);
        else if(!empty($data['currency']))
            return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice'] . $data['currency']);
        else
            return sha1($data['amount'] . $this->config->getMallId() . $this->config->getSharedKey() . $data['invoice']);
    }

    protected function doPrePayment($data){
        $data['req_basket'] = $this->formatBasket($data['req_basket']);

        $ch = curl_init( $this->prePaymentUrl );

        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $responseJson = curl_exec( $ch );

        curl_close($ch);

        return json_decode($responseJson);
    }

    protected function doPayment($data){
        $data['req_basket'] = $this->formatBasket($data['req_basket']);

        $ch = curl_init( $this->paymentUrl );

        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $responseJson = curl_exec( $ch );

        curl_close($ch);

        if(is_string($responseJson)){
            return json_decode($responseJson);
        }else{
            return $responseJson;
        }
    }

    protected function doGeneratePaycode($data){

        $ch = curl_init( $this->generateCodeUrl );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $responseJson = curl_exec( $ch );

        curl_close($ch);

        if(is_string($responseJson)){
            return json_decode($responseJson);
        }else{
            return $responseJson;
        }

    }

    protected function doDirectPayment($data){
        $data['req_basket'] = $this->formatBasket($data['req_basket']);

        $ch = curl_init( $this->directPaymentUrl );

        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, 'data='. json_encode($data));
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

        $responseJson = curl_exec( $ch );

        curl_close($ch);

        if(is_string($responseJson)){
            return json_decode($responseJson);
        }else{
            return $responseJson;
        }
    }

}
