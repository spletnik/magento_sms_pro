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
require_once(Mage::getBaseDir('lib') . '/Spletnisistemi/Smsnotify/NexmoAccount.php');

/**
 * Credit Report Grid
 */
class Spletnisistemi_Smsnotify_Block_CreditReportGrid extends Mage_Adminhtml_Block_Widget_Grid {

    protected function _prepareLayout() {
        parent::_prepareLayout();

        $this->unsetChild('reset_filter_button');
        $this->unsetChild('search_button');

        return $this;
    }

    protected function _prepareCollection() {
        $collection = new Varien_Data_Collection();

        $helper = Mage::helper('smsnotify');

        $runs = array();

        $stores = Mage::app()->getStores();
        foreach ($stores as $store) {
            if ($helper->isActive($store)) {
                $key = $helper->getKey($store);
                $hash = md5($key . ':' . $url);

                if (!isset($runs[$hash])) {
                    $runs[$hash] = array(
                        'key'      => $key,
                        'provider' => "Nexmo",
                        'stores'   => array()
                    );
                }

                $runs[$hash]['stores'][] = $store->getId();
            }
        }

        //$api = Mage::getModel('Mediaburst_Sms/Api', $helper);

        /* @var $api Mediaburst_Sms_Model_Api */

        $results = array();

        foreach ($runs as $hash => $run) {
            $helper->setDefaultStore(reset($run['stores']));
            $api = new NexmoAccount($helper->getKey(), $helper->getSecret());
            $credits = $api->balance();

            $item = new Varien_Object();
            $item->setKey($run['key']);
            $item->setProvider($run['provider']);
            $item->setCredits($credits . " €");

            $collection->addItem($item);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn(
            'provider',
            array(
                'header' => $this->__('Service provider'),
                'index'  => 'provider',
                'filter' => false,
            )
        );

        $this->addColumn(
            'key',
            array(
                'header' => $this->__('Key'),
                'index'  => 'key',
                'filter' => false,
            )
        );

        $this->addColumn(
            'credits',
            array(
                'header' => $this->__('Balance'),
                'index'  => 'credits',
                'filter' => false,
            )
        );

        return parent::_prepareColumns();
    }

    /*public function registerBuyButton()
    {
        $container = $this->getParentBlock();
        if ($container instanceof Mage_Adminhtml_Block_Widget_Grid_Container) {
            $helper = Mage::helper('smsnotify');
            $container->addButton(
                'buy',
                array(
                     'label'   => $this->__('Buy Messages'),
                     'onclick' => 'setLocation(\'http://www.clockworksms.com/platforms/magento/?utm_source=magentoadmin&utm_medium=plugin&utm_campaign=magento\')',
                     'class'   => 'add',
                )
            );
        }
    }*/
}
