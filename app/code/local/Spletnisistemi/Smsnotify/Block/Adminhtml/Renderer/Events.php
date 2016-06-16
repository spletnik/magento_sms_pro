<?php

class Spletnisistemi_Smsnotify_Block_Adminhtml_Renderer_Events extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    private $labels = array(
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CREATED                => 'Order Created',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_HELD                   => 'Order Held',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_UNHELD                 => 'Order Unheld',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_SHIPPED                => 'Shipped',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CANCELED               => 'Order Canceled',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING             => 'Order Processing',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_COMPLETE               => 'Order Complete',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CLOSED                 => 'Order Closed',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PAYMENT_REVIEW         => 'Order Payment Review',

        // Processing sub statuses
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_COD         => 'Processing - COD',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED30M  => 'Delayed for 30 min',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED1H   => 'Delayed for 1 hour',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_OUTOFSTOCK  => 'Out of stock',
        Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_REPLACEMENT => 'Product replacement',

    );

    public function render(Varien_Object $row) {
        $event = $row['event'];
        if (isset($this->labels[$event]))
            return Mage::helper('smsnotify')->__($this->labels[$event]);
        return "Event-$event";
    }
}
