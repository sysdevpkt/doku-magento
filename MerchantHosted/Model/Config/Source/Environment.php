<?php
namespace Doku\MerchantHosted\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Magento\Catalog\Helper\Category;

class Environment implements ArrayInterface{

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
                'value' => $key .'-'. $value,
                'label' => $value
            ];
        }

        return $ret;
    }

    public function toArray()
    {

        $catagoryList = array();

        $catagoryList['Staging'] = __('Staging');
        $catagoryList['Production'] = __('Production');

        return $catagoryList;
    }

}
