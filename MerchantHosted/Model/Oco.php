<?php
namespace Doku\MerchantHosted\Model;

/**
 * Pay In Store payment method model
 */
class Oco extends \Magento\Payment\Model\Method\AbstractMethod
{

    /**
     * Payment code
     *
     * @var string
     */
    protected $_code = 'oco';

    /**
     * Availability option
     *
     * @var bool
     */
    protected $_isOffline = true;


  

}
