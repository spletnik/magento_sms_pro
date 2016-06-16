<?php

class Spletnisistemi_Smsnotify_Helper_Data extends Mage_Core_Helper_Abstract {
    const XML_CONFIG_BASE_PATH = 'smsnotify/';

    protected $_defaultStore = null;

    public function setDefaultStore($store) {
        $this->_defaultStore = $store;
    }

    public function isActive($store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        return Mage::getStoreConfigFlag(self::XML_CONFIG_BASE_PATH . 'general/active', $store);
    }


    public function getKey($store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        return Mage::getStoreConfig(self::XML_CONFIG_BASE_PATH . 'general/key', $store);
    }

    public function getSecret($store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        return Mage::getStoreConfig(self::XML_CONFIG_BASE_PATH . 'general/secret', $store);
    }

    public function getFrom($store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        return Mage::getStoreConfig(self::XML_CONFIG_BASE_PATH . 'general/from', $store);
    }

    public function getShopOwnerNumber($store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        return Mage::getStoreConfig(self::XML_CONFIG_BASE_PATH . 'general/shopowner', $store);
    }

    public function log($message, $level = Zend_Log::DEBUG, $store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        if ($message instanceof Exception) {
            $message = "\n" . $message->__toString();
            $level = Zend_Log::ERR;
            $file = Mage::getStoreConfig('dev/log/exception_file', $store);
        } else {
            if (is_array($message) || is_object($message)) {
                $message = print_r($message, true);
            }
            $file = Mage::getStoreConfig('dev/log/file', $store);
        }

        if ($level < Zend_Log::DEBUG || $this->isDebug($store)) {
            $force = ($level <= Zend_Log::ERR);
            Mage::log($message, $level, $file, $force);
        }
    }

    public function isDebug($store = null) {
        if ($store === null) {
            $store = $this->_defaultStore;
        }

        return Mage::getStoreConfigFlag(self::XML_CONFIG_BASE_PATH . 'general/debug', $store);
    }

    public function generateContent(Mage_Sales_Model_Order $order, $content) {
        $filter = Mage::getModel('core/email_template_filter');
        $filter->setPlainTemplateMode(true);
        $filter->setStoreId($order->getStoreId());
        $filter->setVariables(array('order' => $order));
        return $filter->filter($content);
    }


    public function generateShippedContent(Mage_Sales_Model_Order $order, Mage_Sales_Model_Order_Shipment $shipment, $content) {
        $filter = Mage::getModel('core/email_template_filter');
        $filter->setPlainTemplateMode(true);
        $filter->setStoreId($order->getStoreId());
        $filter->setVariables(
            array(
                'order'    => $order,
                'shipment' => $shipment
            )
        );
        return $filter->filter($content);
    }


    /**
     * Convert a result array into a series of session messages
     *
     * @param Mage_Core_Model_Session_Abstract $session
     *
     * @return Spletnisistemi_Smsnotify_Helper_Data
     */
    public function reportResults(Mage_Core_Model_Session_Abstract $session, array $result) {
        foreach ($result['sent'] as $message) {
            $session->addSuccess($this->__('Sent message %s to %s', $message->getId(), $message->getTo()));
        }
        foreach ($result['failed'] as $message) {
            $session->addError($this->__('Failed sending message %s to %s (%s: %s)', $message->getId(), $message->getTo(), $message->getErrorNumber(), $message->getErrorDescription()));
        }
        foreach ($result['errors'] as $error) {
            $session->addError(implode(' / ', $error));
        }

        return $this;
    }

    public function getTelephone(Mage_Sales_Model_Order $order) {
        $billingAddress = $order->getBillingAddress();

        $number = $billingAddress->getTelephone();
        $number = preg_replace('#[^\+\d]#', '', trim($number));

        if (substr($number, 0, 1) === '+') {
            $number = substr($number, 1);
        } elseif (substr($number, 0, 2) === '00') {
            $number = substr($number, 2);
        } else {
            // Handle special case where mobile numbers are prefixed with a 0
            if (substr($number, 0, 1) === '0') {
                $number = substr($number, 1);
            }

            // Find the telephone dialing code for the billing country
            $expectedPrefix = Zend_Locale_Data::getContent(Mage::app()->getLocale()->getLocale(), 'phonetoterritory', $billingAddress->getCountry());

            // If we couldn't find the dialing code by billing country, chose the store level default
            /*if (empty($expectedPrefix)) {
                $expectedPrefix = Mage::getStoreConfig(self::XML_CONFIG_BASE_PATH . 'general/failsafe_prefix', $store);
            }*/

            // Try to prepend the dialing prefix if it's not part of the number already (Not bullet-proof)
            if (!empty($expectedPrefix)) {
                $prefix = substr($number, 0, strlen($expectedPrefix));
                if ($prefix !== $expectedPrefix) {
                    $number = $expectedPrefix . $number;
                }
            }
        }

        // Final trim of number, Just-In-Case™
        $number = preg_replace('#[^\d]#', '', trim($number));

        return $number;
    }

    /**
     *
     * @param Mage_Sales_Model_Order $order
     * @param string $comment
     *
     * @return Spletnisistemi_Smsnotify_Helper_Data
     */
    public function addOrderComment(Mage_Sales_Model_Order $order, $comment) {
        Mage::getModel('sales/order_status_history')
            ->setOrder($order)
            ->setStatus($order->getStatus())
            ->setComment($comment)
            ->setIsCustomerNotified(true)
            ->save();

        return $this;
    }

}