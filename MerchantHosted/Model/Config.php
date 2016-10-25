<?php

namespace Doku\MerchantHosted\Model;

use \Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    const KEY_MALL_ID = 'mall_id';
    const KEY_SHARED_KEY = 'shared_key';

    protected $methodCode = 'oco';
    protected $scopeConfig;
    protected $storeId = null;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function getMallId()
    {
        return $this->getConfigData(self::KEY_MALL_ID);
    }

    public function getSharedKey()
    {
        return $this->getConfigData(self::KEY_SHARED_KEY);
    }

    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }

    public function getConfigData($field, $storeId = null)
    {
        if ($storeId == null) {
            $storeId = $this->storeId;
        }

        $code = $this->methodCode;

        $path = 'payment/' . $code . '/' . $field;
        return $this->scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

}
