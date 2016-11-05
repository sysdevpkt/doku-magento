<?php

namespace Doku\MerchantHosted\Model\Resource;

use Magento\Framework\Model\Resource\Db\AbstractDb;

class Orders extends AbstractDb{
    protected function _construct(){
        $this->_init('doku_orders', 'id');
    }
}