<?php
namespace Doku\MerchantHosted\Controller\Payment;

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
        $this->customer = $customer->getCustomer();
    }

    public function execute()
    {
        $this->logger->info('===== Token Controller ===== Start');

        try{

            $this->logger->info('customer : '. json_encode($this->customer->convertToArray(), JSON_PRETTY_PRINT));
            $getToken = $this->resourceConnection->getConnection()->select()->from('doku_tokenization')
                ->where('customer_id=?', $this->customer->getEntityId());
            $findToken = $this->resourceConnection->getConnection()->fetchRow($getToken);
            $this->logger->info('token : '. json_encode($findToken, JSON_PRETTY_PRINT));

            $arr = ['err' => 'false', 'res_response_code' => '0001', 'res_response_msg' => 'Success'
                , 'res_response_token' => ($findToken ? $findToken : false)];

            $this->logger->info('===== Token Controller ===== End');

        }catch(\Exception $e){
            $arr = ['err' => 'true', 'res_response_code' => '0001', 'res_response_msg' => $e->getMessage()];

            $this->logger->info('===== Token Controller ===== Check token error : '. $e->getMessage());
            $this->logger->info('===== Token Controller ===== End');
        }

        return json_encode($arr);

    }

}