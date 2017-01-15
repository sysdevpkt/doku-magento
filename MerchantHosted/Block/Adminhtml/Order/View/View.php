<?php
namespace Doku\MerchantHosted\Block\Adminhtml\Order\View;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

class View extends Template{

    protected $session;
    protected $order;
    protected $logger;

    public function __construct(
        LoggerInterface $logger,
        Order $order,
        Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->logger = $logger;
        $this->order = $order;
    }

}