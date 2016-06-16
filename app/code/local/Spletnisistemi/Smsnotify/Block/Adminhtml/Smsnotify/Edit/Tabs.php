<?php

class Spletnisistemi_Smsnotify_Block_Adminhtml_Smsnotify_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {

    public function __construct() {
        parent::__construct();
        $this->setId('smsnotify_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('smsnotify')->__('Event Information'));
    }

    protected function _beforeToHtml() {
        $this->addTab('form_section', array(
            'label'   => Mage::helper('smsnotify')->__('Event Information'),
            'title'   => Mage::helper('smsnotify')->__('Event Information'),
            'content' => $this->getLayout()->createBlock('smsnotify/adminhtml_smsnotify_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}