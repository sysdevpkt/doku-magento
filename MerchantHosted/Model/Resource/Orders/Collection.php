<?php

namespace \Doku\MerchantHosted\Model\Resource\Orders;

use Magento\Framework\Model\Resource\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection{
    protected function _construct(){
        $this->_init(
            'Doku\MerchantHosted\Model\Orders',
            'Doku\MerchantHosted\Model\Resource\Orders'
        );

    }
}