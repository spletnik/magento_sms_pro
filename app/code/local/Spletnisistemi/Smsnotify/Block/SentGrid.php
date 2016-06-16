<?php
/**
 * Mediaburst SMS Magento Integration
 *
 * Copyright © 2011 by Mediaburst Limited
 *
 * Permission to use, copy, modify, and/or distribute this software for any
 * purpose with or without fee is hereby granted, provided that the above
 * copyright notice and this permission notice appear in all copies.
 *
 * THE SOFTWARE IS PROVIDED "AS IS" AND ISC DISCLAIMS ALL WARRANTIES WITH REGARD
 * TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY AND
 * FITNESS. IN NO EVENT SHALL ISC BE LIABLE FOR ANY SPECIAL, DIRECT, INDIRECT,
 * OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM LOSS OF
 * USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR OTHER
 * TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR PERFORMANCE
 * OF THIS SOFTWARE.
 *
 * @category  Mage
 * @package   Mediaburst_Sms
 * @license   http://opensource.org/licenses/isc-license.txt
 * @copyright Copyright © 2011 by Mediaburst Limited
 * @author    Lee Saferite <lee.saferite@lokeycoding.com>
 */

/**
 * Sent Message Grid
 */
class Spletnisistemi_Smsnotify_Block_SentGrid extends Spletnisistemi_Smsnotify_Block_AbstractMessageGrid {

    protected function _filterCollection(Varien_Data_Collection_Db $collection) {
        $collection->addFieldToFilter('status', Spletnisistemi_Smsnotify_Model_Smses::STATUS_SENT); //->setOrder('created_at', 'DESC')
        return $this;
    }

    protected function _prepareColumns() {
        $this->addColumnAfter(
            'message_id',
            array(
                'header' => $this->__('MessageID'),
                'index'  => 'message_id',
                'filter' => false,
            ),
            'content'
        );
        $this->addColumnAfter(
            'cost',
            array(
                'header' => $this->__('SMS price'),
                'index'  => 'cost',
                'filter' => false,
            ),
            'content'
        );

        if (Mage::getSingleton('admin/session')->isAllowed('sales/spletnisistemi_smsnotify/requeue')) {
            $this->addColumnAfter(
                'action',
                array(
                    'header'    => $this->__('Action'),
                    'width'     => '50px',
                    'type'      => 'action',
                    'getter'    => 'getId',
                    'filter'    => false,
                    'sortable'  => false,
                    'is_system' => true,
                    'actions'   => array(
                        array(
                            'caption' => $this->__('Requeue'),
                            'url'     => array('base' => '*/*/requeue'),
                            'field'   => 'id'
                        )
                    )
                ),
                'message_id'
            );
        }

        return parent::_prepareColumns();
    }
}