<?php

namespace Doku\MerchantHosted\Block;

use Magento\Framework\App\ResourceConnection;

class Success extends \Magento\Checkout\Block\Onepage\Success
{

    protected $resourceConnection;

    public function __construct(
        ResourceConnection $resourceConnection
    )
    {
        $this->resourceConnection = $resourceConnection;
    }

    public function getOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }

    public function getPaycode(){

        $order = $this->getOrder();

        $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
            ->where('quote_id=?', $order->getQuoteId())->where('store_id=?', $order->getStoreId())
            ->where('order_id=?', $order->getId());
        $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

        return $findOrder['paycode_no'];

    }

    public function checkPaymentChannel(){

        $order = $this->getOrder();

        $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
            ->where('quote_id=?', $order->getQuoteId())->where('store_id=?', $order->getStoreId())
            ->where('order_id=?', $order->getId());
        $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

        if($findOrder['payment_channel_id'] != '04' && $findOrder['payment_channel_id'] != '15')
            return true;
        else
            return false;

    }

}