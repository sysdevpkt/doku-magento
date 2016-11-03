<?php

namespace Doku\MerchantHosted\Plugin\Magento\Checkout\Model\Session;

use Magento\Framework\Api\Search\SearchCriteriaFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use \Psr\Log\LoggerInterface;

class SuccessValidator
{
    protected $session;
    protected $orderCollectionFactory;
    protected $logger;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Checkout\Model\Session $session,
        LoggerInterface $logger
    ) {
        $this->session = $session;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->logger = $logger;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {
        $this->logger->info('masuk');
        $this->logger->info('session : '. json_encode($this->session->getLastRealOrder()->convertToArray(), JSON_PRETTY_PRINT));

        return $returnValue;
    }
}