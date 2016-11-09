<?php 

namespace Doku\MerchantHosted\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use \Magento\Framework\App\Config\ScopeConfigInterface;

class DokuConfigProvider implements ConfigProviderInterface
{

    const mall_id = 'mall_id';
    const shared_key = 'shared_key';
    const payment_channels = 'payment_channels';
    const payment_title = 'title';
    const paycode = 'paycode';
    const pc = ['14' => 'alfa', '08' => 'mandiri_lite', '09' => 'mandiri_full', '05' => 'permata_lite', '07' => 'permata_full',
        '21' => 'sinarmas_lite', '22' => 'sinarmas_full'];

    protected $scopeConfig;
    protected $logger;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ){
        $this->scopeConfig = $scopeConfig;
    }

    public function getMallId()
    {
        return $this->scopeConfig->getValue('payment/core/'. self::mall_id, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSharedKey()
    {
        return $this->scopeConfig->getValue('payment/core/'. self::shared_key, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPaycode($pc){
        return $this->scopeConfig->getValue('payment/'. self::pc[$pc] .'/'. self::paycode, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getPaymentChannels()
    {
        $pcs = explode(',', $this->scopeConfig->getValue('payment/core/'. self::payment_channels, \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
        $payment_channels = array();

        foreach ($pcs as $pc) {
            $payment_channels[] = explode('-', $pc);
        }

        return json_encode($payment_channels);
    }

    public function getPaymentTitle()
    {
        return $this->scopeConfig->getValue('payment/core/'. self::payment_title, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getConfig()
    {
        $config = [
            'payment' => [
                'core' => [
                    'mall_id' => $this->getMallId(),
                    'shared_key' => $this->getSharedKey(),
                    'payment_channels' => $this->getPaymentChannels(),
                    'payment_title' => $this->getPaymentTitle()
                ]
            ]
        ];
        return $config;
    }
}