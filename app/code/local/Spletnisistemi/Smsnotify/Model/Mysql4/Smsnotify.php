<?php

class Spletnisistemi_Smsnotify_Model_Mysql4_Smsnotify extends Mage_Core_Model_Mysql4_Abstract {
    public function _construct() {
        // Note that the web_id refers to the key field in your database table.
        $this->_init('smsnotify/smsnotify', 'smsnotify_id');
    }
}