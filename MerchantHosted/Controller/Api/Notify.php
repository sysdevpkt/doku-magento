<?php
namespace Doku\MerchantHosted\Controller\Api;

use Doku\MerchantHosted\Controller\Payment\Library;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Payment\Transaction\BuilderInterface;

class Notify extends Library {

    protected $resourceConnection;
    private $order;


    public function __construct(
        LoggerInterface $logger,
        Context $context,
        DokuConfigProvider $config,
        ResourceConnection $resourceConnection,
        Order $order,
        BuilderInterface $builderInterface
    )
    {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->resourceConnection = $resourceConnection;
        $this->order = $order;
        $this->builderInterface = $builderInterface;
    }

    public function execute()
    {
        $this->logger->info('===== Notify Controller ===== Start');

        try{

            $this->logger->info('post : '. json_encode($_POST, JSON_PRETTY_PRINT));

            $postData = $_POST;
            $words = sha1($postData['AMOUNT'] . $this->config->getMallId() . $this->config->getSharedKey()
                . $postData['TRANSIDMERCHANT'] . $postData['RESULTMSG'] . $postData['VERIFYSTATUS']);

            $this->logger->info('words raw : '. $postData['AMOUNT'] . $this->config->getMallId() . $this->config->getSharedKey()
                . $postData['TRANSIDMERCHANT'] . $postData['RESULTMSG'] . $postData['VERIFYSTATUS']);
            $this->logger->info('words : '. $words);
            $this->logger->info('===== Notify Controller ===== Checking words...');

            if($postData['WORDS'] == $words){

                $this->logger->info('===== Notify Controller ===== Checking done');
                $this->logger->info('===== Notify Controller ===== Finding order...');

                $getOrder = $this->resourceConnection->getConnection()->select()->from('doku_orders')
                    ->where('invoice_no=?', $postData['TRANSIDMERCHANT']);
                $findOrder = $this->resourceConnection->getConnection()->fetchRow($getOrder);

                $this->logger->info('===== Notify Controller ===== Order found');
                $this->logger->info('===== Notify Controller ===== Updating order...');

                $saveOrder = $this->order->load($findOrder['order_id']);

                $payment = $saveOrder->getPayment();
                $payment->setLastTransactionId($postData['TRANSIDMERCHANT']);
                $payment->setTransactionId($postData['TRANSIDMERCHANT']);
                $payment->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $_POST]);
                $message = __(json_encode($_POST, JSON_PRETTY_PRINT));
                $trans = $this->builderInterface;
                $transaction = $trans->setPayment($payment)
                  ->setOrder($order)
                  ->setTransactionId($postData['TRANSIDMERCHANT'])
                  ->setAdditionalInformation([\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $_POST])
                  ->setFailSafe(true)
                  ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);
                $payment->addTransactionCommentsToOrder($transaction, $message);
                $payment->setParentTransactionId(null);
                $payment->save();
                $saveOrder->save();
                $transaction->save();
                $this->resourceConnection->getConnection()->update('doku_orders',
                    ['order_status' => 'SUCCESS'], ['invoice_no=?' => $postData['TRANSIDMERCHANT']]);

                $this->logger->info('===== Notify Controller ===== Updating success...');
                echo 'CONTINUE';

                /*$saveOrder->setState(Order::STATE_PROCESSING);
                $saveOrder->setStatus(Order::STATE_PROCESSING);

                if($saveOrder->save()){
                    $this->resourceConnection->getConnection()->update('doku_orders',
                        ['order_status' => 'SUCCESS'], ['invoice_no=?' => $postData['TRANSIDMERCHANT']]);

                    $this->logger->info('===== Notify Controller ===== Updating success...');
                    echo 'CONTINUE';

                }else{

                    $this->logger->info('===== Notify Controller ===== Updating failed...');
                    echo 'STOP';

                } */

            }else{
                $this->logger->info('===== Notify Controller ===== Words not match!');
                echo 'STOP';
            }

            $this->logger->info('===== Notify Controller ===== End');

        }catch(\Exception $e){
            $this->logger->info('===== Notify Controller ===== Generate code error : '. $e->getMessage());
            $this->logger->info('===== Notify Controller ===== End');

            echo 'STOP';
        }

    }

}
