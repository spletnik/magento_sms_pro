<?php

class Spletnisistemi_Smsnotify_Block_Adminhtml_Renderer_Sendingto extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row) {
        if ($row["sendingto"] == 1) {
            return "Customer";
        }
        if ($row["sendingto"] == 2) {
            return "Shop owner";
        }
        if ($row["sendingto"] == 3) {
            return "Customer & Shop owner";
        }
        return $data;
    }
}