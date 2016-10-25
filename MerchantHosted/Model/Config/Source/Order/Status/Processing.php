<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Doku\MerchantHosted\Model\Config\Source\Order\Status;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Config\Source\Order\Status;

/**
 * Order Status source model
 */
class Processing extends Status
{
    /**
     * @var string[]
     */
    protected $_stateStatuses = [Order::STATE_PROCESSING];
}