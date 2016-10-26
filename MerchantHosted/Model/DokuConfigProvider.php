<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ){
        $this->_scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->_scopeConfig->getValue('payment/oco/mall_id', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'shared_key' => $this->_scopeConfig->getValue('payment/oco/shared_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                    'currency' => $this->_storeManager->getStore()->getCurrentCurrencyCode()
                ]
            ]
        ];
        return $config;
    }
}