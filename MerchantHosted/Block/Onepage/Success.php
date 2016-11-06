<?php
namespace Doku\MerchantHosted\Block\Onepage;

use \Magento\Framework\View\Element\Template;

class Success extends Template
{
    public function getSomething()
    {
        return 'returned something from custom block.';
    }
}