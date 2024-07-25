<?php
namespace I95Dev\PoNumber\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

class Info extends \Magento\Backend\Block\Template
{
    protected $orderRepository;
    protected $logger;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
        parent::__construct($context, $data);
    }

    public function getPurchaseOrderNumber()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        $paymentMethod = "";
        if ($orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                $payment = $order->getPayment();
                $paymentMethod = $payment->getMethod();
                if ($payment) {
                    return array('method' => $paymentMethod, 'po_number' => $payment->getData('po_number'));
                } else {
                    $this->logger->error('Payment information not found for order ID: ' . $orderId);
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->logger->error('Order not found with ID: ' . $orderId);
            } catch (\Exception $e) {
                $this->logger->error('An error occurred while loading the order: ' . $e->getMessage());
            }
        } else {
            $this->logger->error('Order ID parameter is missing.');
        }
        return null;
    }
}
