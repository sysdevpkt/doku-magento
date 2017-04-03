<?php

namespace Doku\MerchantHosted\Plugin\Magento\Checkout\Model\Session;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use Magento\Framework\App\ResourceConnection;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\DataObject;
use Doku\MerchantHosted\Model\DokuConfigProvider;

class SuccessValidator
{
    protected $session;
    protected $order;
    protected $logger;
    protected $resourceConnection;
    private $transportBuilder;
    private $dataObject;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order,
        ResourceConnection $resourceConnection,
        TransportBuilder $transportBuilder,
        DataObject $dataObject
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
        $this->resourceConnection = $resourceConnection;
        $this->transportBuilder = $transportBuilder;
        $this->dataObject = $dataObject;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {

        $this->logger->info('===== afterIsValid ===== Start');

        try{

            $order = $this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId());

            $this->logger->info('===== afterIsValid ===== Updating order...');

            $this->resourceConnection->getConnection()
                ->update('doku_orders', ['order_id' => $order->getId()],
                    ["quote_id=?" => $order->getQuoteId(), "store_id=?" => $order->getStoreId()]);

            $this->logger->info('===== afterIsValid ===== Updating complete');
            $this->logger->info('===== afterIsValid ===== Checking status...');

            $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                ->where('quote_id=?', $order->getQuoteId())->where('store_id=?', $order->getStoreId());
            $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

            if($findOrder['payment_channel_id'] != '15' && $findOrder['payment_channel_id'] != '04'
                && $findOrder['payment_channel_id'] != '02'){
                $order->setStatus(Order::STATE_PENDING_PAYMENT);
                $order->setState(Order::STATE_PENDING_PAYMENT);
                $this->session->getLastRealOrder()->setStatus(Order::STATE_PENDING_PAYMENT);
                $this->session->getLastRealOrder()->setState(Order::STATE_PENDING_PAYMENT);
                $order->save();
            }

            $this->logger->info('===== afterIsValid ===== Checking done');
            $this->logger->info('===== afterIsValid ===== Sending email...');

            $emailVar = [
                'subject' => "Pay your order [". $findOrder['paycode_no'] ."] Via [". DokuConfigProvider::pcName[$findOrder['payment_channel_id']]
                    ."] - [". $order->getStoreName() ."]" ,
                'customerName' => $order->getCustomerName(),
                'pcName' => DokuConfigProvider::pcName[$findOrder['payment_channel_id']],
                'storeName' => $order->getStoreName(),
                'invoiceNo' => $findOrder['invoice_no'],
                'payCode' => $findOrder['paycode_no'],
                'amount' => $order->getGrandTotal()
            ];

            $this->dataObject->setData($emailVar);

            $sender = [
                'name' => 'Doku',
                'email' => 'no-reply@doku.com',
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier('paycode_template')->setFrom($sender)
                ->addTo($order->getCustomerEmail(), $order->getCustomerName())
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => $order->getStoreId()
                    ]
                )->setTemplateVars(['data' => $this->dataObject])
                ->getTransport();
            $transport->sendMessage();

            $this->logger->info('===== afterIsValid ===== Sending done');

        }catch(\Exception $e){
            $this->logger->info('error : '. $e->getMessage());
        }

        $this->logger->info('===== afterIsValid ===== End');

        return $returnValue;
    }
}