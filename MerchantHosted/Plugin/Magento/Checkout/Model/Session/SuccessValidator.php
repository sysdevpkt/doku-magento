<?php

namespace Doku\MerchantHosted\Plugin\Magento\Checkout\Model\Session;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResourceConnection;

class SuccessValidator
{
    protected $session;
    protected $order;
    protected $logger;
    protected $resourceConnection;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order,
        ResourceConnection $resourceConnection
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
        $this->resourceConnection = $resourceConnection;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {

        try{

            $order = $this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId());

            $this->logger->info('get order: '. json_encode($order, JSON_PRETTY_PRINT));

            $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                ->where('quote_id', $order->getQuoteId())->where('store_id', $order->getStoreId());
            $findOrder = $this->resourceConnection->getConnection()->fetchAll($getOrder);

            $this->logger->info('find order db: '. json_encode($findOrder, JSON_PRETTY_PRINT));

            $this->resourceConnection->getConnection()
                ->update('doku_orders', ['order_id' => $order->getId()],
                    ["quote_id = ?" => $order->getQuoteId(), "store_id = ?" => $order->getStoreId()]);

            $order->setState(Order::STATE_PENDING_PAYMENT);

            $this->logger->info('get order 2 : '. json_encode($this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId()), JSON_PRETTY_PRINT));

        }catch(\Exception $e){
            $this->logger->info('error : '. $e->getMessage());
        }

        return $returnValue;
    }
}