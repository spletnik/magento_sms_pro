<?php

class Spletnisistemi_Smsnotify_Model_Mysql4_Smsnotify_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
    public function _construct() {
        parent::_construct();
        $this->_init('smsnotify/smsnotify');
    }
}