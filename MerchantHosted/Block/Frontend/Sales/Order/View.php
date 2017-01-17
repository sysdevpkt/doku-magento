<?php

namespace Doku\MerchantHosted\Block\FrontEnd\Sales\Order;

use Magento\Framework\View\Element\Template\Context;
use \Magento\Framework\Registry;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\ResourceConnection;
use Doku\MerchantHosted\Model\DokuConfigProvider;

class View extends \Magento\Framework\View\Element\Template{

    private $registry;
    private $logger;
    private $resourceConnection;

    public function __construct(
        Context $context,
        array $data = [],
        Registry $registry,
        Logger $logger,
        ResourceConnection $resourceConnection
    ){
        parent::__construct(
           $context, $data
        );

        $this->registry = $registry;
        $this->logger = $logger;
        $this->resourceConnection = $resourceConnection;
    }

    private function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    public function getOrderData(){

        $this->logger->info('===== Block View ===== Start');

        try{

            $findOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                ->where('quote_id=?', $this->getOrder()->getQuoteId())->where('store_id=?', $this->getOrder()->getStoreId());
            $rowOrder = $this->resourceConnection->getConnection()->fetchRow($findOrder);
            $orderInfo = ['channel_id' => $rowOrder['payment_channel_id'], 'channel_name' => DokuConfigProvider::pcName[$rowOrder['payment_channel_id']]];

        }catch(\Exception $e){
            $this->logger->info('===== Block View ===== getOrderData error : '. $e->getMessage());
            $orderInfo = ['channel_id' => '', 'channel name' => ''];
        }

        $this->logger->info('===== Block View ===== End');

        return $orderInfo;

    }

}