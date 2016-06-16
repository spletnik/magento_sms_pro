<?php
require_once(Mage::getBaseDir('lib') . '/Spletnisistemi/Smsnotify/NexmoMessage.php');

/**
 * Event Observer
 */
class Spletnisistemi_Smsnotify_Model_Observer {
    /**
     *    naredi se new order sms, ce je new order event nastavljen
     */
    public function createOrderCreatedMessage(Varien_Event_Observer $observer) {

        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */
            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CREATED)
                ->getFirstItem();
            if ($smsnotify_event->status == 1) {
                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    public function createSMSEntityForCustomer($order, $smsnotify_event) {
        try {
            $message = Mage::getModel('smsnotify/smses');
            $message->setStoreId($order->getStoreId());
            $message->setTo($this->getHelper()->getTelephone($order));
            $message->setFrom($this->getHelper()->getFrom());
            $message->setContent($this->getHelper()->generateContent($order, $smsnotify_event->template));
            $message->save();
            $this->getHelper()->addOrderComment($order, 'SMS message generated (' . $message->getId() . ') by Spletnisistemi - Smsnotify');
        } catch (Exception $e) {
            $this->getHelper()->log('Error creating Order Created SMS Message Record for Order ' . $order->getIncrementId(), Zend_Log::ERR);
        }
    }

    /**
     *
     * @return Spletnisistemi_Smsnotify_Helper_Data
     */
    public function getHelper() {
        return Mage::helper('smsnotify');
    }

    public function createSMSEntityForShopOwner($order, $smsnotify_event) {
        try {
            $message = Mage::getModel('smsnotify/smses');
            $message->setStoreId($order->getStoreId());
            $message->setTo($this->getHelper()->getShopOwnerNumber());
            $message->setFrom($this->getHelper()->getFrom());
            $message->setContent($this->getHelper()->generateContent($order, $smsnotify_event->template));
            $message->save();
            $this->getHelper()->addOrderComment($order, 'SMS message generated (' . $message->getId() . ') by Spletnisistemi - Smsnotify');
        } catch (Exception $e) {
            $this->getHelper()->log('Error creating Order Created SMS Message Record for Order ' . $order->getIncrementId(), Zend_Log::ERR);
        }
    }

    /**
     *   naredi se held sms, ce je held event nastavljen
     */
    public function createOrderHeldMessage(Varien_Event_Observer $observer) {
        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_HELD)
                ->getFirstItem();


            if ($order->getState() !== $order->getOrigData('state') &&
                $order->getState() === Mage_Sales_Model_Order::STATE_HOLDED &&
                $smsnotify_event->status == 1
            ) {

                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     *    naredi se unheld sms, ce je unheld event nastavljen
     */
    public function createOrderUnheldMessage(Varien_Event_Observer $observer) {

        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_UNHELD)
                ->getFirstItem();

            if ($order->getState() !== $order->getOrigData('state') &&
                $order->getOrigData('state') === Mage_Sales_Model_Order::STATE_HOLDED &&
                $smsnotify_event->status == 1
            ) {
                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     *   naredi se shipped sms, ce je shipped event nastavljen
     */
    public function createOrderShippedMessage(Varien_Event_Observer $observer) {
        $shipment = $observer->getShipment();
        if ($shipment instanceof Mage_Sales_Model_Order_Shipment) {
            /* @var $shipment Mage_Sales_Model_Order_Shipment */
            $order = $shipment->getOrder();
            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_SHIPPED)
                ->getFirstItem();
            if ($smsnotify_event->status == 1) {
                try {
                    $message = Mage::getModel('smsnotify/smses');
                    $message->setStoreId($order->getStoreId());
                    $message->setTo($this->getHelper()->getTelephone($order));
                    $message->setFrom($this->getHelper()->getFrom());
                    $message->setContent($this->getHelper()->generateShippedContent($order, $shipment, $smsnotify_event->template));
                    $message->save();
                    $this->getHelper()->addOrderComment($order, 'SMS message generated (' . $message->getId() . ') by Spletnisistemi - Smsnotify');
                } catch (Exception $e) {
                    $this->getHelper()->log('Error creating Order Shipped SMS Message Record for Order ' . $order->getIncrementId(), Zend_Log::ERR);
                }
            }
        }
    }

    /**
     *   naredi se shipped sms, ce je canceled event nastavljen
     */
    public function createOrderCanceledMessage(Varien_Event_Observer $observer) {
        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CANCELED)
                ->getFirstItem();


            if ($order->getState() !== $order->getOrigData('state') &&
                $order->getState() === Mage_Sales_Model_Order::STATE_CANCELED &&
                $smsnotify_event->status == 1
            ) {

                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     *   naredi se shipped sms, ce je processing event nastavljen
     */
    public function createOrderProcessingMessage(Varien_Event_Observer $observer) {
        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $sub_status = $order->getStatus();
            switch ($sub_status) {
                case 'processing':
                    $sub_status = Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING;
                    break;
                case 'processing_cod_confirm':
                    $sub_status = Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_COD;
                    break;
                case 'Order Delayed for 30 mins.':
                    $sub_status = Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED30M;
                    break;
                case 'order_delayed_morethan 1 hour':
                    $sub_status = Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED1H;
                    break;
                case 'productoutofstock':
                    $sub_status = Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_OUTOFSTOCK;
                    break;
                case 'productreplacement':
                    $sub_status = Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_REPLACEMENT;
                    break;
                default:
                    return;
            }

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', $sub_status)
                ->getFirstItem();


            if (($order->getState() !== $order->getOrigData('state')
                    || $order->getStatus() !== $order->getOrigData('status'))
                && $order->getState() === Mage_Sales_Model_Order::STATE_PROCESSING
                && $smsnotify_event->status == 1
            ) {

                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     *   naredi se shipped sms, ce je complete event nastavljen
     */
    public function createOrderCompleteMessage(Varien_Event_Observer $observer) {
        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_COMPLETE)
                ->getFirstItem();


            if ($order->getState() !== $order->getOrigData('state') &&
                $order->getState() === Mage_Sales_Model_Order::STATE_COMPLETE &&
                $smsnotify_event->status == 1
            ) {

                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     *   naredi se shipped sms, ce je closed event nastavljen
     */
    public function createOrderClosedMessage(Varien_Event_Observer $observer) {
        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CLOSED)
                ->getFirstItem();


            if ($order->getState() !== $order->getOrigData('state') &&
                $order->getState() === Mage_Sales_Model_Order::STATE_CLOSED &&
                $smsnotify_event->status == 1
            ) {

                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     *   naredi se shipped sms, ce je payment_review event nastavljen
     */
    public function createOrderPaymentReviewMessage(Varien_Event_Observer $observer) {
        $order = $observer->getOrder();
        if ($order instanceof Mage_Sales_Model_Order) {
            /* @var $order Mage_Sales_Model_Order */

            $smsnotify_event = Mage::getModel('smsnotify/smsnotify')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('event', Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PAYMENT_REVIEW)
                ->getFirstItem();


            if ($order->getState() !== $order->getOrigData('state') &&
                $order->getState() === Mage_Sales_Model_Order::STATE_PAYMENT_REVIEW &&
                $smsnotify_event->status == 1
            ) {

                if ($smsnotify_event->sendingto == 1 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForCustomer($order, $smsnotify_event);
                }
                if ($smsnotify_event->sendingto == 2 || $smsnotify_event->sendingto == 3) {
                    $this->createSMSEntityForShopOwner($order, $smsnotify_event);
                }
            }
        }
    }

    /**
     * Cron Job - poslje se sms
     */
    public function sendPendingMessages() {
        /*$collection = Mage::getModel('smsnotify/smses')->getCollection()
        ->addFieldToSelect('*')
        ->addFieldToFilter('status', Spletnisistemi_Smsnotify_Model_Smses::STATUS_PENDING);
        */

        $runs = array();

        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            if ($this->getHelper()->isActive($store)) {
                $runs['stores'][] = $store->getId();
            }
        }

        $collection = Mage::getModel('smsnotify/smses')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('status', Spletnisistemi_Smsnotify_Model_Smses::STATUS_PENDING)
            ->addFieldToFilter('store_id', $runs['stores'])
            ->setPageSize(100);

        $api = new NexmoMessage($this->getHelper()->getKey(), $this->getHelper()->getSecret());
        foreach ($collection as $sms) {
            $resp = $api->sendText($sms->to, $this->getHelper()->getFrom(), $sms->content);


// Build an array of each message status and ID
            if (!is_array($resp->messages)) $resp->messages = array();
            $message_status = array();
            foreach ($resp->messages as $message) {
                $tmp = array('id' => '', 'status' => $message->status);

                if ($message->status != 0) {
                    $tmp['error-text'] = $message->errortext;
                } else {
                    $tmp['id'] = $message->messageid;
                    //$tmp['message-price'] = $message->cost;
                }
                $message_status[] = $tmp;
            }

            if ($message_status[0]["status"] == 0) {
                $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_SENT);
                $sms->setMessageID($message_status[0]["id"]);
                $sms->setData("cost", $resp->cost);
            } else {
                $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_FAILED);
                $sms->setData("error_number", $message_status[0]["status"]);
                $sms->setData("error_description", $message_status[0]["error-text"]);
            }
            $sms->save();
        }

        $this->getHelper()->setDefaultStore(null);

    }
}