<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    protected $config;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Doku\MerchantHosted\Model\DokuConfig $config
    ){
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->config->getMallId(),
                    'shared_key' => $this->config->getSharedKey(),
                    'currency' => $this->storeManager->getStore()->getCurrentCurrencyCode()
                ]
            ]
        ];
        return $config;
    }
}