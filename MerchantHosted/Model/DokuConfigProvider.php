<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    private $mall_id = 'mall_id';
    private $shared_key = 'shared_key';

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Doku\MerchantHosted\Model\DokuConfig $config
    ){
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getMallId()
    {
        return $this->scopeConfig->getValue('payment/oco/'. $this->mall_id, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSharedKey()
    {
        return $this->scopeConfig->getValue('payment/oco/'. $this->shared_key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->getMallId(),
                    'shared_key' => $this->getSharedKey(),
                    'currency' => $this->storeManager->getStore()->getCurrentCurrencyCode()
                ]
            ]
        ];
        return $config;
    }
}