<?php

class Spletnisistemi_Smsnotify_Block_Adminhtml_Smsnotify_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'smsnotify';
        $this->_controller = 'adminhtml_smsnotify';

        $this->_updateButton('save', 'label', Mage::helper('smsnotify')->__('Save Event'));
        $this->_updateButton('delete', 'label', Mage::helper('smsnotify')->__('Delete Event'));

        $this->_addButton('saveandcontinue', array(
            'label'   => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class'   => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('smsnotify_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'smsnotify_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'smsnotify_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }

    public function getHeaderText() {
        if (Mage::registry('smsnotify_data') && Mage::registry('smsnotify_data')->getId()) {
            return Mage::helper('smsnotify')->__("Edit Event '%s'", $this->htmlEscape(Mage::registry('smsnotify_data')->getTitle()));
        } else {
            return Mage::helper('smsnotify')->__('Add Event');
        }
    }
}