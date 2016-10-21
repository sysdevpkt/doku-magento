<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Doku\MerchantHosted\Model;



/**
 * Pay In Store payment method model
 */
class Oco extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code            = 'oco';
    protected $_canUseCheckout  = true;
    protected $_canUseInternal  = true;
    protected $_isGateway       = true;


  

}
