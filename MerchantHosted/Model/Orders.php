<?php

namespace Doku\MerchantHosted\Model;

use Magento\Framework\Model\AbstractModel;

class Orders extends AbstractModel{

    protected function _construct()
    {
        $this->_init('Doku\MerchantHosted\Model\Resource\Orders');
    }

}