<?php

namespace I95Dev\PoNumber\Observer\Frontend\Sales;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\OfflinePayments\Model\Purchaseorder;
use Magento\Framework\App\Request\DataPersistorInterface;

class OrderPaymentSaveBefore implements \Magento\Framework\Event\ObserverInterface
{
    protected $order;
    protected $logger;
    protected $_serialize;
    protected $quoteRepository;

    public function __construct(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Serialize\Serializer\Serialize $serialize
    ) {
        $this->order = $order;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->_serialize = $serialize;
    }
    /**
     * Execute observer
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writerr = new \Zend_Log_Writer_Stream(BP . '/var/log/mylogger.log');
        $loggerr = new \Zend_Log();
        $loggerr->addWriter($writerr);

        $orderids = $observer->getEvent()->getOrderIds();
        $loggerr->info('order ids:'.print_r($orderids, true));
        if($orderids){
            foreach ($orderids as $orderid) {
                $order = $this->order->load($orderid);
                $method = $order->getPayment()->getMethod();
                $loggerr->info('Method: '.$method);
                if($method == 'purchaseorder') {
                    $quote_id = $order->getQuoteId();
                    $loggerr->info('quoteId: '.$quote_id);
                    $quote = $this->quoteRepository->get($quote_id);
                    $paymentQuote = $quote->getPayment();
                    $paymentOrder = $order->getPayment();
                    $loggerr->info('po comment: '.$paymentQuote->getPaymentpocomment());
                    $paymentOrder->setData('paymentpocomment',$paymentQuote->getPaymentpocomment());
                    $paymentOrder->save();
                }
            }
        }
    }
}
