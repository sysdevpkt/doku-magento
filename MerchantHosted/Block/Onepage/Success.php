<?php
namespace Doku\MerchantHosted\Block\Onepage;

use \Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResourceConnection;

class Success extends Template
{

    protected $session;
    protected $order;
    protected $logger;
    protected $resourceConnection;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order,
        ResourceConnection $resourceConnection,
        Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
        $this->resourceConnection = $resourceConnection;
    }

    protected function getOrder(){
        return $order = $this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId());
    }

    public function getPaycode()
    {
        $this->logger->info('===== getPaycode ===== Start');

        try{

            $order = $this->getOrder();
            $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                ->where('quote_id=?', $order->getQuoteId())->where('store_id=?', $order->getStoreId());
            $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

            return $findOrder['paycode_no'];

        }catch(\Exception $e){
            $this->logger->info('error : '. $e->getMessage());
        }

        $this->logger->info('===== getPaycode ===== End');

    }

    public function checkPaymentChannel()
    {
        $this->logger->info('===== checkPaymentChannel ===== Start');

        try{

            $order = $this->getOrder();
            $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                ->where('quote_id=?', $order->getQuoteId())->where('store_id=?', $order->getStoreId());
            $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

            if($findOrder['payment_channel_id'] != '04' && $findOrder['payment_channel_id'] != '15'
                && $findOrder['payment_channel_id'] != '02') return true;
            else return false;

        }catch(\Exception $e){
            $this->logger->info('error : '. $e->getMessage());
        }

        $this->logger->info('===== checkPaymentChannel ===== End');

    }
}