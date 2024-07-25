<?php
namespace I95Dev\PoNumber\Observer\Frontend\Sales;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\OfflinePayments\Model\Purchaseorder;
use Magento\OfflinePayments\Model\Checkmo;
use Magento\OfflinePayments\Model\Cashondelivery;
use Magento\OfflinePayments\Model\Banktransfer;
class PaymentSaveBefore implements ObserverInterface {
    protected $_inputParamsResolver;
    protected $_quoteRepository;
    protected $logger;
    protected $_state;

    public function __construct(
        \Magento\Webapi\Controller\Rest\InputParamsResolver $inputParamsResolver,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Psr\Log\LoggerInterface $logger,\Magento\Framework\App\State $state
    ) {
        $this->_inputParamsResolver = $inputParamsResolver;
        $this->_quoteRepository = $quoteRepository;
        $this->logger = $logger;
        $this->_state = $state;
    }

    public function execute(EventObserver $observer) {
        $objManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objManager->get(\Magento\Framework\App\Request\Http::class);
        $route = $request->getRouteName();
        if(empty($route)){
            $inputParams = $this->_inputParamsResolver->resolve();
            if($this->_state->getAreaCode() != \Magento\Framework\App\Area::AREA_ADMINHTML){
                foreach ($inputParams as $inputParam) {
                    if ($inputParam instanceof \Magento\Quote\Model\Quote\Payment) {
                        $paymentData = $inputParam->getData('additional_data');
                        $paymentOrder = $observer->getEvent()->getPayment();
                        $order = $paymentOrder->getOrder();
                        $quote = $this->_quoteRepository->get($order->getQuoteId());
                        $paymentQuote = $quote->getPayment();
                        $method = $paymentQuote->getMethodInstance()->getCode();
                        if ($method == Purchaseorder::PAYMENT_METHOD_PURCHASEORDER_CODE) {
                            if(isset($paymentData['po_number'])){
                                $paymentQuote->setData('po_number', $paymentData['po_number']);
                                $paymentQuote->setData('paymentpocomment', $paymentData['paymentpocomment']);
                                $paymentOrder->setData('po_number', $paymentData['po_number']);
                                $paymentOrder->setData('paymentpocomment', $paymentData['paymentpocomment']);
                            }
                        }elseif ($method == Checkmo::PAYMENT_METHOD_CHECKMO_CODE || $method == Banktransfer::PAYMENT_METHOD_BANKTRANSFER_CODE || $method == Cashondelivery::PAYMENT_METHOD_CASHONDELIVERY_CODE) {
                            $paymentQuote->setData('po_number', $paymentData['po_number']);
                            $paymentOrder->setData('po_number', $paymentData['po_number']);
                        }
                    }
                }
            }
        }

    }
}
