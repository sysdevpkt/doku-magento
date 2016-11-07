<?php
namespace Doku\MerchantHosted\Controller\Api;

use Doku\MerchantHosted\Controller\Payment\Library;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Framework\App\ResourceConnection;

class Notify extends Library{

    protected $resourceConnection;

    public function __construct(
        LoggerInterface $logger,
        Context $context,
        DokuConfigProvider $config,
        ResourceConnection $resourceConnection
    )
    {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->resourceConnection = $resourceConnection;
    }

    public function execute()
    {
        $this->logger->info('===== notify Controller ===== Start');

        try{

            $this->logger->info('post : '. json_encode($_POST, JSON_PRETTY_PRINT));
            $this->logger->info('===== notify Controller ===== End');

        }catch(\Exception $e){
            $this->logger->info('===== orderva Controller ===== Generate code error : '. $e->getMessage());
            $this->logger->info('===== orderva Controller ===== End');

            echo 'STOP';
        }

    }

}