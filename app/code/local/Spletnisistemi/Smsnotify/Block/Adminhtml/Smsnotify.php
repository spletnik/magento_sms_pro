<?php

class Spletnisistemi_Smsnotify_Block_Adminhtml_Smsnotify extends Mage_Adminhtml_Block_Widget_Grid_Container {
    public function __construct() {
        $this->_controller = 'adminhtml_smsnotify';
        $this->_blockGroup = 'smsnotify';
        $this->_headerText = Mage::helper('smsnotify')->__('Event Manager');
        $this->_addButtonLabel = Mage::helper('smsnotify')->__('Add Event');
        parent::__construct();
    }
}