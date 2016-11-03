<?php

namespace Doku\MerchantHosted\Controller\Onepage;

use \Psr\Log\LoggerInterface;

class Success extends \Magento\Checkout\Controller\Onepage\Success
{

    protected $logger;

    public function __construct(
        LoggerInterface $logger
    )
    {
        $this->logger = $logger;
    }

    public function execute()
    {

        $this->logger->info('execute checkout success');

        return parent::execute();
    }

}