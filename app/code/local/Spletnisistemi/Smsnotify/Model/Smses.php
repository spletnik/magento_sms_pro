<?php

class Spletnisistemi_Smsnotify_Model_Smses extends Mage_Core_Model_Abstract {

    const STATUS_PENDING = 0;
    const STATUS_SENT = 1;
    const STATUS_FAILED = 2;
    const DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';

    public function _construct() {
        parent::_construct();
        $this->_init('smsnotify/smses');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave() {
        parent::_beforeSave();

        $this->setCreatedAt($this->_toDbDate($this->getCreatedAt()));
        $this->setUpdatedAt($this->_toDbDate());

        return $this;
    }

    /**
     * Convert a date string or object into a MySQL formatted date-time string
     *
     * @param Zend_Date|string|null $date
     *
     * @return string
     */
    protected function _toDbDate($date = null) {
        if (!$date instanceof Zend_Date) {
            if (empty($date)) {
                $date = new Zend_Date();
            } else {
                $date = new Zend_Date(strtotime($date), Zend_Date::TIMESTAMP);
            }
        }

        return $date->toString(self::DATETIME_FORMAT);
    }

    /**
     * Processing object after save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave() {
        parent::_afterSave();

        $this->setCreatedAt($this->_fromDbDate($this->getCreatedAt()));
        $this->setUpdatedAt($this->_fromDbDate($this->getUpdatedAt()));

        return $this;
    }

    /**
     * Convert a MySQL Formatted DateTime string into a Zend_Date object
     *
     * @param string $date
     *
     * @return Zend_Date
     */
    protected function _fromDbDate($date) {
        if (!$date instanceof Zend_Date) {
            $date = strtotime($date);

            if ($date === false) {
                $date = new Zend_Date();
            } else {
                $date = new Zend_Date($date, Zend_Date::TIMESTAMP);
            }
        }

        return $date;
    }

    /**
     * Processing object after load data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterLoad() {
        parent::_afterLoad();

        $this->setCreatedAt($this->_fromDbDate($this->getCreatedAt()));
        $this->setUpdatedAt($this->_fromDbDate($this->getUpdatedAt()));

        return $this;
    }
}