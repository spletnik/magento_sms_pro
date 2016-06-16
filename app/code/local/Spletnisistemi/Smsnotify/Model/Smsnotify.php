<?php

class Spletnisistemi_Smsnotify_Model_Smsnotify extends Mage_Core_Model_Abstract {

    const EVENT_CREATED = 1;
    const EVENT_HELD = 2;
    const EVENT_UNHELD = 3;
    const EVENT_SHIPPED = 4;
    const EVENT_CANCELED = 5;
    const EVENT_PROCESSING = 6;
    const EVENT_COMPLETE = 7;
    const EVENT_CLOSED = 8;
    const EVENT_PAYMENT_REVIEW = 9;

    // Sub-statuses (processing)
    const EVENT_PROCESSING_COD = 101;
    const EVENT_PROCESSING_DELAYED30M = 102;
    const EVENT_PROCESSING_DELAYED1H = 103;
    const EVENT_PROCESSING_OUTOFSTOCK = 104;
    const EVENT_PROCESSING_REPLACEMENT = 105;

    const SENDINGTO_USER = 1;
    const SENDINGTO_OWNER = 2;
    const SENDINGTO_BOTH = 3;

    public function _construct() {
        parent::_construct();
        $this->_init('smsnotify/smsnotify');
    }
}