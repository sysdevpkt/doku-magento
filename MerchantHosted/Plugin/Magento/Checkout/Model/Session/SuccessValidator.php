<?php

namespace Doku\MerchantHosted\Plugin\Magento\Checkout\Model\Session;

use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Magento\Checkout\Model\Session;
use \Magento\Quote\Api\CartRepositoryInterface;
use \Magento\Quote\Model\QuoteRepository;

class SuccessValidator
{
    protected $session;
    protected $order;
    protected $logger;
    protected $quote;
    protected $quote2;

    public function __construct(
        Session $session,
        LoggerInterface $logger,
        Order $order,
        CartRepositoryInterface $quote,
        QuoteRepository $quoteRepository
    ) {
        $this->session = $session;
        $this->logger = $logger;
        $this->order = $order;
        $this->quote = $quote;
        $this->quote2 = $quoteRepository;
    }

    public function afterIsValid(\Magento\Checkout\Model\Session\SuccessValidator $successValidator, $returnValue)
    {
        $this->logger->info('masuk');
        $this->logger->info('session : '. json_encode($this->session->getLastRealOrder()->convertToArray(), JSON_PRETTY_PRINT));
        $this->logger->info('order : '. json_encode($this->order->loadByIncrementId($this->session->getLastRealOrder()->getIncrementId())->convertToArray(), JSON_PRETTY_PRINT));
        $this->logger->info('quote : '. json_encode($this->quote->get($this->session->getLastRealOrder()->getQuoteId()), JSON_PRETTY_PRINT));
        $this->logger->info('quote2 : '. json_encode($this->quote2->get($this->session->getLastRealOrder()->getQuoteId())->convertToArray(), JSON_PRETTY_PRINT));


        return $returnValue;
    }
}