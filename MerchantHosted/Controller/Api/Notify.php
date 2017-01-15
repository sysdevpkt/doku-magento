<?php
namespace Doku\MerchantHosted\Controller\Api;

use Doku\MerchantHosted\Controller\Payment\Library;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\OrderRepository;

class Notify extends Library implements OrderRepositoryInterface{

    protected $resourceConnection;
    private $orderRepositoryInterface;
    private $orderRepository;

    public function __construct(
        LoggerInterface $logger,
        Context $context,
        DokuConfigProvider $config,
        ResourceConnection $resourceConnection,
        OrderRepositoryInterface $orderRepositoryInterface,
        OrderRepository $orderRepository
    )
    {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->resourceConnection = $resourceConnection;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderRepository = $orderRepository;
    }

    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $this->orderRepository->getList($searchCriteria);
    }

    public function get($id)
    {
        $this->orderRepository->get($id);
    }

    public function delete(\Magento\Sales\Api\Data\OrderInterface $entity)
    {
        $this->orderRepository->delete($entity);
    }

    public function save(\Magento\Sales\Api\Data\OrderInterface $entity)
    {
        $this->orderRepository->save($entity);
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

                $order = $this->get($findOrder['order_id']);
                $order->setState(Order::STATE_PROCESSING);
                $order->setStatus(Order::STATE_PROCESSING);

                if($this->save()){
                    $this->resourceConnection->getConnection()->update('doku_orders',
                        ['order_status' => 'SUCCESS'], ['invoice_no=?' => $postData['TRANSIDMERCHANT']]);

                    $this->logger->info('===== Notify Controller ===== Updating success...');
                    echo 'CONTINUE';

                }else{

                    $this->logger->info('===== Notify Controller ===== Updating failed...');
                    echo 'STOP';

                }

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