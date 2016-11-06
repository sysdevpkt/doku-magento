<?php

namespace Doku\MerchantHosted\Block\Checkout;

use \Magento\Framework\View\Element\Template;

class Success extends Template
{

    public function getOrder(){
        return 'order blablabla';
    }

}