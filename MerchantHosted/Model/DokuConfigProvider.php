<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    public function __construct(
        \Doku\MerchantHosted\Model\Config $config
    ) {
        $this->config = $config;
    }
    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->config->getMallId(),
                    'shared_key' => $this->config->getSharedKey
                ]
            ]
        ];
        return $config;
    }
}