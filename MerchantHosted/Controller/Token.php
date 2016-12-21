<?php
namespace Doku\MerchantHosted\Controller;

use Doku\MerchantHosted\Controller\Payment\Library;
use \Psr\Log\LoggerInterface;
use \Magento\Framework\App\Action\Context;
use Doku\MerchantHosted\Model\DokuConfigProvider;
use Magento\Framework\App\ResourceConnection;
use \Magento\Customer\Model\Session;

class Token extends Library{

    protected $resourceConnection;
    protected $customer;

    public function __construct(
        LoggerInterface $logger,
        Context $context,
        DokuConfigProvider $config,
        ResourceConnection $resourceConnection,
        Session $customer
    )
    {
        parent::__construct(
            $logger,
            $context,
            $config
        );

        $this->resourceConnection = $resourceConnection;
        $this->customer = $customer;
    }

    public function execute()
    {
        $this->logger->info('===== Token Controller ===== Start');
        $arr = [];

        try{

            $this->logger->info('customer : '. json_encode($this->customer->getCustomer()->convertToArray(), JSON_PRETTY_PRINT));

            $arr = ['err' => 'false', 'res_response_code' => '0001', 'res_response_msg' => 'Success'];

            $this->logger->info('===== Token Controller ===== End');

        }catch(\Exception $e){
            $arr = ['err' => 'true', 'res_response_code' => '0001', 'res_response_msg' => $e->getMessage()];

            $this->logger->info('===== Token Controller ===== Check token error : '. $e->getMessage());
            $this->logger->info('===== Token Controller ===== End');
        }

        return json_encode($arr);

    }

}