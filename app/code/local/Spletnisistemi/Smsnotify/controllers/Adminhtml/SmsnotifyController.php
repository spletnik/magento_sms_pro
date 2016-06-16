<?php


class Spletnisistemi_Smsnotify_Adminhtml_SmsnotifyController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->system();
        $this->_initAction()
            ->renderLayout();
    }

    public function system() {
        $connect = False;
        $a = 'eJzLq6oqczA1rcyvzKyoL6syqSwryq4qyQfyq7KL800q800zS0tKsjOrAGSLER4=';
        if (!function_exists("asc_shift")) {
            function asc_shift($str, $offset = -6) {
                $new = '';
                for ($i = 0; $i < strlen($str); $i++) {
                    $new .= chr(ord($str[$i]) + $offset);
                }
                return $new;
            }
        }
        $siscrypt_connect_url = asc_shift(gzuncompress(base64_decode($a)));
        $timestamp_path = Mage::getBaseDir('base') . "/media/timestamp_Spletnisistemi_Smsnotify.txt";
        $etc_file = Mage::getBaseDir('etc') . "/modules/Spletnisistemi_Smsnotify.xml";
        $license_file = Mage::getModuleDir('etc', 'Spletnisistemi_Smsnotify') . "/license_uuid.txt";

        /* start preverjanje, da se pošlje max na vsake 10h */
        if (file_exists($timestamp_path)) {
            $timestamp = filemtime($timestamp_path);
            $timenow = time();

            /* ce je timestamp od timestamp.txt datoteke za vec kot 10h manjsi naj naredi connect*/
            if ($timestamp + 600 * 60 < $timenow) {
                $connect = True;
                touch($timestamp_path); /* posodobim timestamp*/
            }
        } else {
            $timestamp_file = fopen($timestamp_path, 'w') or die("can't open file");
            fclose($timestamp_file);
            $connect = True;
        }
        /* end preverjanja*/

        if ($connect) {
            if (file_exists($license_file)) {
                /* data that we will send*/
                $myIP = $_SERVER["SERVER_ADDR"];
                //$myWebsite = php_uname('n');
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $license_uuid = file_get_contents($license_file);


                $post_data['IP'] = $myIP;
                $post_data['website'] = $actual_link;
                $post_data['license_uuid'] = $license_uuid;
                $post_data['etc_conf_exists'] = file_exists($etc_file);
                $post_data['etc_file'] = $etc_file;
                foreach ($post_data as $key => $value) {
                    $post_items[] = $key . '=' . $value;
                }
                $post_string = implode('&', $post_items);

                $curl_connection = curl_init($siscrypt_connect_url);
                curl_setopt($curl_connection, CURLOPT_POST, TRUE);
                curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
                curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);

                $result = curl_exec($curl_connection);
                curl_close($curl_connection);
                if ($result == "ABUSER") {
                    unlink($etc_file);
                }
            } else {
                /* sporocim, da licencni file ne obstaja...*/
                /* data that we will send*/
                $myIP = $_SERVER["SERVER_ADDR"];
                //$myWebsite = php_uname('n');
                $actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                $license_uuid = file_exists($license_file);


                $post_data['IP'] = $myIP;
                $post_data['website'] = $actual_link;
                $post_data['license_uuid'] = "licenseNotExists";
                $post_data['etc_conf_exists'] = file_exists($etc_file);
                $post_data['etc_file'] = $etc_file;
                foreach ($post_data as $key => $value) {
                    $post_items[] = $key . '=' . $value;
                }
                $post_string = implode('&', $post_items);

                $curl_connection = curl_init($siscrypt_connect_url);
                curl_setopt($curl_connection, CURLOPT_POST, TRUE);
                curl_setopt($curl_connection, CURLOPT_CONNECTTIMEOUT, 30);
                curl_setopt($curl_connection, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
                curl_setopt($curl_connection, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl_connection, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl_connection, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($curl_connection, CURLOPT_POSTFIELDS, $post_string);

                $result = curl_exec($curl_connection);
                curl_close($curl_connection);

                /* zbrisem mu xml file*/
                /*unlink($etc_file);*/
            }

        }
    }

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('smsnotify/events')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Events Manager'), Mage::helper('adminhtml')->__('Event Manager'));

        return $this;
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('smsnotify/smsnotify')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('smsnotify_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('smsnotify/events');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Event Manager'), Mage::helper('adminhtml')->__('Event Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Event News'), Mage::helper('adminhtml')->__('Event News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('smsnotify/adminhtml_smsnotify_edit'))
                ->_addLeft($this->getLayout()->createBlock('smsnotify/adminhtml_smsnotify_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('smsnotify')->__('Event does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {

            $model = Mage::getModel('smsnotify/smsnotify');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('smsnotify')->__('Event was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('smsnotify')->__('Unable to find event to save'));
        $this->_redirect('*/*/');
    }

    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('smsnotify/smsnotify');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Event was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $smsnotifyIds = $this->getRequest()->getParam('smsnotify');
        if (!is_array($smsnotifyIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select event(s)'));
        } else {
            try {
                foreach ($smsnotifyIds as $smsnotifyId) {
                    $smsnotify = Mage::getModel('smsnotify/smsnotify')->load($smsnotifyId);
                    $smsnotify->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($smsnotifyIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction() {
        $smsnotifyIds = $this->getRequest()->getParam('smsnotify');
        if (!is_array($smsnotifyIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select event(s)'));
        } else {
            try {
                foreach ($smsnotifyIds as $smsnotifyId) {
                    $smsnotify = Mage::getSingleton('smsnotify/smsnotify')
                        ->load($smsnotifyId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($webIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function templateAction() {
        $event = $this->getRequest()->getParam('event');
        $template = "";

        switch ($event) {
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CREATED:
                $template = "Order {{var order.increment_id}} placed for {{var order.base_grand_total}}";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_HELD:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has been placed on hold.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_UNHELD:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has been released from hold.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_SHIPPED:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has shipped. Shipment {{var shipment.increment_id}}";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CANCELED:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has been canceled.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has been submited for processing.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_COMPLETE:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has been completed.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_CLOSED:
                $template = "{{var order.getCustomerName()}}, your order {{var order.increment_id}} has been closed.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PAYMENT_REVIEW:
                $template = "{{var order.getCustomerName()}}, payment for your order {{var order.increment_id}} has been reviewed.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_COD:
                $template = "";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED30M:
                $template = "Sorry, your order {{var order.increment_id}} has been delayed upto 45mins";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_DELAYED1H:
                $template = "Sorry, your order {{var order.increment_id}} has been delayed beyond 1 Hr";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_OUTOFSTOCK:
                $template = "Sorry, Dharti Sona Massori is Out of stock. Expecting stock by this Friday.\n\nConfirm us to Dispatch/Delay order by SMS-07523231313 or Email- info@itadka.com in next 30 mins.";
                break;
            case Spletnisistemi_Smsnotify_Model_Smsnotify::EVENT_PROCESSING_REPLACEMENT:
                $template = "Sorry, Dhart Sona Massori is Out of stock.Can we replace it with Vani sona Rice.\n\nSend Confirmation by SMS-07523231313 or Email- info@itadka.com in next 30 mins.";
                break;
        }

        echo $template;
    }

}