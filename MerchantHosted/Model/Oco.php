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
