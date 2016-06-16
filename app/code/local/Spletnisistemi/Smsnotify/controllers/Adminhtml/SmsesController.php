<?php
require_once(Mage::getBaseDir('lib') . '/Spletnisistemi/Smsnotify/NexmoMessage.php');

class Spletnisistemi_Smsnotify_Adminhtml_SmsesController extends Mage_Adminhtml_Controller_action {

    public function indexAction() {
        $this->_redirect('*/*/pending');
    }

    public function checkAction() {
        $this->loadLayout();
        $this->_setActiveMenu('sales/smsnotify/check');
        $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
        $this->renderLayout();
    }

    public function pendingAction() {
        $this->loadLayout();
        $this->_setActiveMenu('sales/smsnotify/pending');
        $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
        $this->renderLayout();
    }

    public function sentAction() {
        $this->loadLayout();
        $this->_setActiveMenu('sales/smsnotify/sent');
        $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
        $this->renderLayout();
    }

    public function failedAction() {
        $this->loadLayout();
        $this->_setActiveMenu('sales/smsnotify/failed');
        $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
        $this->renderLayout();
    }

    public function sendAction() {

        $id = (int)$this->getRequest()->getParam('id');
        $sms = Mage::getModel('smsnotify/smses')->load($id);

        if ($sms->getStatus() == Spletnisistemi_Smsnotify_Model_Smses::STATUS_PENDING) {
            $api = new NexmoMessage($this->getHelper()->getKey(), $this->getHelper()->getSecret());
            $resp = $api->sendText($sms->to, $this->getHelper()->getFrom(), $sms->content);

            // Build an array of each message status and ID
            if (!is_array($resp->messages)) $resp->messages = array();
            $message_status = array();
            foreach ($resp->messages as $message) {
                $tmp = array('id' => '', 'status' => $message->status);

                if ($message->status != 0) {
                    $tmp['error-text'] = $message->errortext;
                } else {
                    $tmp['id'] = $message->messageid;
                    //$tmp['message-price'] = $message->messageprice;
                }

                $message_status[] = $tmp;
            }


            if ($message_status[0]["status"] == 0) {
                $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_SENT);
                $sms->setMessageID($message_status[0]["id"]);
                $sms->setData("cost", $resp->cost);
            } else {
                $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_FAILED);
                $sms->setData("error_number", $message_status[0]["status"]);
                $sms->setData("error_description", $message_status[0]["error-text"]);
            }
            $sms->save();
        }


        foreach ($collection as $sms) {
            $resp = $api->sendText($sms->to, $this->getHelper()->getFrom(), $sms->content);


            $to = 'gasper.vozel@spletnik.si';
            $subject = 'obj vars';
            $message = 'obj vars: ' . get_object_vars($resp);
            $headers = 'From: shop@spletnisistemi.si' . "\r\n" .
                'Reply-To: noreply@spletnisistemi.si' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

            mail($to, $subject, $message, $headers);


            // Build an array of each message status and ID
            if (!is_array($resp->messages)) $resp->messages = array();
            $message_status = array();
            foreach ($resp->messages as $message) {
                $tmp = array('id' => '', 'status' => $message->status);

                if ($message->status != 0) {
                    $tmp['error-text'] = $message->errortext;
                } else {
                    $tmp['id'] = $message->messageid;
                    //$tmp['message-price'] = $message->cost;
                }

                $message_status[] = $tmp;
            }

            if ($message_status[0]["status"] == 0) {
                $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_SENT);
                $sms->setMessageID($message_status[0]["id"]);
                $sms->setData("cost", $resp->cost);
            } else {
                $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_FAILED);
                $sms->setData("error_number", $message_status[0]["status"]);
                $sms->setData("error_description", $message_status[0]["error-text"]);
            }
            $sms->save();
        }


        $this->_redirect('*/*/pending');
    }

    public function getHelper() {
        return Mage::helper('smsnotify');
    }

    public function requeueAction() {
        $id = (int)$this->getRequest()->getParam('id');
        $sms = Mage::getModel('smsnotify/smses')->load($id);

        $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_PENDING);
        $sms->save();

        $this->_redirect('*/*/sent');
    }

    public function retryAction() {
        $id = (int)$this->getRequest()->getParam('id');
        $sms = Mage::getModel('smsnotify/smses')->load($id);

        $sms->setStatus(Spletnisistemi_Smsnotify_Model_Smses::STATUS_PENDING);
        $sms->save();

        $this->_redirect('*/*/failed');
    }

    public function forceCronAction() {
        /* pass */

        $this->_redirect('*/*');
    }
}