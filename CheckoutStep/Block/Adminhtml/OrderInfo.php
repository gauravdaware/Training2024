<?php


namespace I95Dev\CheckoutStep\Block\Adminhtml;

use Magento\Framework\View\Element\Template;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderInfo extends Template
{
    protected $orderRepository;

    public function __construct(
        Template\Context $context,
        OrderRepositoryInterface $orderRepository,
        array $data = []
    )
    {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    public function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->orderRepository->get($orderId);
    }
}
