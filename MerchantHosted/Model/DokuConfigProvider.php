<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    protected $config;

    public function __construct(
        \Doku\MerchantHosted\Model\DokuConfig $config
    ){
        $this->config = $config;
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'oco' => [
                    'mall_id' => $this->config->getMallId(),
                    'shared_key' => $this->config->getSharedKey(),
                ]
            ]
        ];
        return $config;
    }
}