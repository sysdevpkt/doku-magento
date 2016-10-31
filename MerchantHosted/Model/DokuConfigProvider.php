<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    const mall_id = 'mall_id';
    const shared_key = 'shared_key';
    const payment_channels = 'payment_channels';

    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ){
        $this->scopeConfig = $scopeConfig;
    }

    public function getMallId()
    {
        return $this->scopeConfig->getValue('payment/oco/'. self::mall_id, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSharedKey()
    {
        return $this->scopeConfig->getValue('payment/oco/'. self::shared_key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPaymentChannels()
    {
        return $this->scopeConfig->getValue('payment/oco/'. self::payment_channels, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->getMallId(),
                    'shared_key' => $this->getSharedKey(),
                    'payment_channels' => $this->getPaymentChannels()
                ]
            ]
        ];
        return $config;
    }
}