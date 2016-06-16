<?php

class Spletnisistemi_Smsnotify_Block_Smsnotify extends Mage_Core_Block_Template {
    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getSmsnotify() {
        if (!$this->hasData('smsnotify')) {
            $this->setData('smsnotify', Mage::registry('smsnotify'));
        }
        return $this->getData('smsnotify');

    }
}