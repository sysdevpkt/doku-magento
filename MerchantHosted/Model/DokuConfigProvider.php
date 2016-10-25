<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->_scopeConfig = $scopeConfig;
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => 'tes mall id',
                    'shared_key' =>'tes shared key'
                ]
            ]
        ];
        return $config;
    }
}