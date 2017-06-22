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
    protected $config;
    private $transportBuilder;
    private $dataObject;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order,
        ResourceConnection $resourceConnection,
        TransportBuilder $transportBuilder,
        DataObject $dataObject,
        DokuConfigProvider $config
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
        $this->resourceConnection = $resourceConnection;
        $this->transportBuilder = $transportBuilder;
        $this->dataObject = $dataObject;
        $this->config = $config;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {

        $this->logger->info('===== afterIsValid ===== Start');
        date_default_timezone_set('Asia/Jakarta');

        try{

            $this->logger->info('===== afterIsValid ===== getLastRealOrder getIncrementId = ' . $this->session->getLastRealOrder()->getIncrementId());
            $order = $this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId());

            $this->logger->info('===== afterIsValid ===== Updating order...');

            $this->resourceConnection->getConnection()
                ->update('doku_orders', ['order_id' => $order->getId()],
                    ["quote_id=?" => $order->getQuoteId(), "store_id=?" => $order->getStoreId()]);

            $this->logger->info('===== afterIsValid ===== Updating complete');
            $this->logger->info('===== afterIsValid ===== Checking status...');
            $this->logger->info('===== afterIsValid ===== Checking status = ' . $order->getStatus());
            $this->logger->info('===== afterIsValid ===== Checking state = ' . $order->getState());

            $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                ->where('quote_id=?', $order->getQuoteId())->where('store_id=?', $order->getStoreId());
            $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

            if($findOrder['payment_channel_id'] == '41' || $findOrder['payment_channel_id'] == '05'){
                $order->setStatus(Order::STATE_PENDING_PAYMENT);
                $order->setState(Order::STATE_PENDING_PAYMENT);
                $this->session->getLastRealOrder()->setStatus(Order::STATE_PENDING_PAYMENT);
                $this->session->getLastRealOrder()->setState(Order::STATE_PENDING_PAYMENT);
                $order->save();

                $this->logger->info('===== afterIsValid ===== Sending email...');

                $emailVar = [
                    'subject' => "Pay your order [". $findOrder['paycode_no'] ."] Via [". DokuConfigProvider::pcName[$findOrder['payment_channel_id']]
                        ."] - [". $order->getStoreName() ."]" ,
                    'customerName' => $order->getCustomerName(),
                    'pcName' => DokuConfigProvider::pcName[$findOrder['payment_channel_id']],
                    'storeName' => $order->getStoreName(),
                    'invoiceNo' => $findOrder['invoice_no'],
                    'payCode' => $findOrder['paycode_no'],
                    'amount' => $order->getGrandTotal(),
                    'expiry' => date('d/m/Y H:i:s', (strtotime('+' . $this->config->getExpiry() . ' minutes', time())))
                ];

                $this->dataObject->setData($emailVar);

                $sender = [
                    'name' => $this->config->getSenderName(),
                    'email' => $this->config->getSenderMail(),
                ];

                $template = 'paycode_template';
                if($findOrder['payment_channel_id'] == '41') {
                    $template = 'paycode_template_mandiri_va';
                }

                $transport = $this->transportBuilder->setTemplateIdentifier($template)->setFrom($sender)
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

            }

            $this->logger->info('===== afterIsValid ===== Checking done');


        }catch(\Exception $e){
            $this->logger->info('error : '. $e->getMessage());
        }

        $this->logger->info('===== afterIsValid ===== End');

        return $returnValue;
    }
}
