<?php

namespace Doku\MerchantHosted\Plugin\Magento\Checkout\Model\Session;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use \Magento\Quote\Model\QuoteRepository;

class SuccessValidator
{
    protected $session;
    protected $order;
    protected $logger;
    protected $quote;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order,
        QuoteRepository $quote
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
        $this->quote = $quote;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {

        try{

            $this->logger->info('session : '. json_encode($this->session->getLastRealOrder()->convertToArray(), JSON_PRETTY_PRINT));
            $this->logger->info('order : '. json_encode($this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId())->convertToArray(), JSON_PRETTY_PRINT));
            $this->logger->info('session quote : '. json_encode($this->quote->get($this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId())->getQuoteId()), JSON_PRETTY_PRINT));
            $this->logger->info('manual session quote: '. $this->quote->get($this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId())->getQuoteId())->getData('payment_channel'));

        }catch(\Exception $e){
            $this->logger->info('error : '. $e->getMessage());
        }

        return $returnValue;
    }
}