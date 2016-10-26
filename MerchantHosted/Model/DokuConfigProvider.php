<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    const KEY_MALL_ID = 'mall_id';
    const KEY_SHARED_KEY = 'shared_key';
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->scopeConfig = $scopeConfig;
    }

    public function getMallId(){
        return $this->scopeConfig->getValue('payment/oco/'. self::KEY_MALL_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSharedKey(){
        return $this->scopeConfig->getValue('payment/oco/'. self::KEY_SHARED_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->getMallId(),
                    'shared_key' => $this->getSharedKey(),
                ]
            ]
        ];
        return $config;
    }
}