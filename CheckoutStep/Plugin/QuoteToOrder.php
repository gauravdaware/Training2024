<?php

namespace I95Dev\CheckoutStep\Plugin;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
class QuoteToOrder
{
    protected $orderRepository;
    /**
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param OrderInterface $order
     * @param CartInterface $quote
     * @return array
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }
    public function beforeSubmit(\Magento\Quote\Model\QuoteManagement $subject, CartInterface $quote, $orderData = [])
    {
        $writerr = new \Zend_Log_Writer_Stream(BP . '/var/log/mylogger.log');
        $loggerr = new \Zend_Log();
        $loggerr->addWriter($writerr);
        $loggerr->info('beforeSubmit:');
        try{
            $extensionAttributes = $quote->getBillingAddress()->getExtensionAttributes();
            $loggerr->info('Extension Attributes:'.json_encode($extensionAttributes));
            if ($extensionAttributes && $extensionAttributes->getPreferredContact()) {
                $preferredContact = $extensionAttributes->getPreferredContact();
                 $loggerr->info('Preferred Contact'.$preferredContact);
                //$quote->getBillingAddress()->setPreferredContact($preferredContact);
                $quote->setData('preferred_contact', $preferredContact);
            }
            return [$quote, $orderData];
        }catch (\Exception $e){
            $loggerr->info($e->getMessage());
        }
    }

    /**
     * @param \Magento\Quote\Model\QuoteManagement $subject
     * @param OrderInterface $order
     * @param CartInterface $quote
     * @return OrderInterface
     */
    public function afterSubmit(\Magento\Quote\Model\QuoteManagement $subject, OrderInterface $order, CartInterface $quote)
    {
        try{
            $writerr = new \Zend_Log_Writer_Stream(BP . '/var/log/mylogger.log');
            $loggerr = new \Zend_Log();
            $loggerr->addWriter($writerr);
            $loggerr->info('afterSubmit:');
            $preferredContact = $quote->getData('preferred_contact');
            $loggerr->info('preferredContact: '.$preferredContact);
            $loggerr->info('order id: '.$order->getIncrementId());
            $orderId = $order->getId();
            $orderData = $this->orderRepository->get($orderId);
            $orderData->setData('preferred_contact', $preferredContact);
            $this->orderRepository->save($orderData);
            return $order;
        }catch (\Exception $e){
            throw new \Magento\Framework\Exception\LocalizedException($e->getMessage());
        }
    }
}
