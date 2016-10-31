<?php
namespace Doku\MerchantHosted\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class PaymentChannel implements ArrayInterface{

    protected $categoryHelper;

    public function __construct(
        Category $catalogCategory
    )
    {
        $this->_categoryHelper = $catalogCategory;
    }

    public function toOptionArray()
    {

        $arr = $this->toArray();
        $ret = [];

        foreach ($arr as $key => $value)
        {

            $ret[] = [
                'value' => $key,
                'label' => $value
            ];
        }

        return $ret;
    }

    public function toArray()
    {

        $catagoryList = array();

        $catagoryList['04'] = __('Doku Wallet');
        $catagoryList['15'] = __('Credit Card');

        return $catagoryList;
    }

}