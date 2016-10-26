<?php

namespace Doku\MerchantHosted\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class DokuConfig
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

    /**
     * @return string
     */
    public function getMallId()
    {
        return $this->getConfigData(self::KEY_MALL_ID);
    }

    public function getSharedKey()
    {
        return $this->getConfigData(self::KEY_SHARED_KEY);
    }

    public function getConfigData($field)
    {
        $code = $this->methodCode;

        $path = 'payment/' . $code . '/' . $field;
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
