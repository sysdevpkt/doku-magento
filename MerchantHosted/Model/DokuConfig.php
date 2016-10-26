<?php

namespace Doku\MerchantHosted\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

abstract class DokuConfig implements ScopeConfigInterface
{
    const KEY_MALL_ID = 'mall_id';
    const KEY_SHARED_KEY = 'shared_key';

    protected $methodCode = 'oco';
    protected $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getMallId(){
        return $this->scopeConfig->getValue('payment/oco/'. self::KEY_MALL_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSharedKey(){
        return $this->scopeConfig->getValue('payment/oco/'. self::KEY_SHARED_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}
