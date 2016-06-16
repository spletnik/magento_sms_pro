<?php

class Spletnisistemi_Smsnotify_Block_Adminhtml_Smsnotify_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('smsnotify_form', array('legend' => Mage::helper('smsnotify')->__('Event information')));


        $fieldset->addField('status', 'select', array(
            'label'  => Mage::helper('smsnotify')->__('Status'),
            'name'   => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('smsnotify')->__('Enabled'),
                ),

                array(
                    'value' => 2,
                    'label' => Mage::helper('smsnotify')->__('Disabled'),
                ),
            ),
        ));

        $event = $fieldset->addField('event', 'select', array(
            'label'    => Mage::helper('smsnotify')->__('Send on (event)'),
            'name'     => 'event',
            'onchange' => 'gettemplate(this)',
            'values'   => array(
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CREATED,
                    'label' => Mage::helper('smsnotify')->__('Order Created'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_HELD,
                    'label' => Mage::helper('smsnotify')->__('Order Held'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_UNHELD,
                    'label' => Mage::helper('smsnotify')->__('Order Unheld'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_SHIPPED,
                    'label' => Mage::helper('smsnotify')->__('Order Shipped'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CANCELED,
                    'label' => Mage::helper('smsnotify')->__('Order Canceled'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING,
                    'label' => Mage::helper('smsnotify')->__('Order Processing'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_COMPLETE,
                    'label' => Mage::helper('smsnotify')->__('Order Complete'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CLOSED,
                    'label' => Mage::helper('smsnotify')->__('Order Closed'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PAYMENT_REVIEW,
                    'label' => Mage::helper('smsnotify')->__('Order Payment Review'),
                ),

                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_COD,
                    'label' => Mage::helper('smsnotify')->__('Processing - COD'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED30M,
                    'label' => Mage::helper('smsnotify')->__('Delayed for 30 min'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED1H,
                    'label' => Mage::helper('smsnotify')->__('Delayed for 1 hour'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_OUTOFSTOCK,
                    'label' => Mage::helper('smsnotify')->__('Out of stock'),
                ),
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_REPLACEMENT,
                    'label' => Mage::helper('smsnotify')->__('Product replacement'),
                ),
            ),
        ));


        /*
         * Add Ajax to the Event select 
         */
        $event->setAfterElementHtml("<script type=\"text/javascript\">
            function gettemplate(selectElement){
                var reloadurl = '" . $this
                ->getUrl('smsnotify/adminhtml_smsnotify/template') . "event/' + selectElement.value;
                new Ajax.Request(reloadurl, {
                    method: 'get',
                    onLoading: function (template) {
                        $('template').update('Searching...');
                    },
                    onComplete: function(template) {
                        $('template').update(template.responseText);
                    }
                });
            }
        </script>");


        $fieldset->addField('sendingto', 'select', array(
            'label'  => Mage::helper('smsnotify')->__('Send SMS to'),
            'name'   => 'sendingto',
            'values' => array(
                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::SENDINGTO_USER,
                    'label' => Mage::helper('smsnotify')->__('Customer'),
                ),

                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::SENDINGTO_OWNER,
                    'label' => Mage::helper('smsnotify')->__('Shop owner'),
                ),

                array(
                    'value' => Spletnisistemi_Smsnotify_Model_Smsnotify::SENDINGTO_BOTH,
                    'label' => Mage::helper('smsnotify')->__('Customer & Shop owner'),
                )
            ),
        ));

        $fieldset->addField('template', 'editor', array(
            'name'     => 'template',
            'label'    => Mage::helper('smsnotify')->__('Message Template'),
            'title'    => Mage::helper('smsnotify')->__('Message Template'),
            'style'    => 'width:280px; height:100px;',
            'wysiwyg'  => false,
            'required' => true,
        ));

        if (Mage::getSingleton('adminhtml/session')->getSmsnotifyData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getSmsnotifyData());
            Mage::getSingleton('adminhtml/session')->setSmsnotifyData(null);
        } elseif (Mage::registry('smsnotify_data')) {
            $form->setValues(Mage::registry('smsnotify_data')->getData());
        }
        return parent::_prepareForm();
    }
}