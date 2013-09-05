<?php

    class Comfirm_AlphaMail_Block_EventLog_View extends Mage_Catalog_Block_Product_Abstract {
    	private $_send_id = null;

        public function __construct() {
            parent::__construct();
            $this->setTemplate('alphamail/logging/event_view.phtml');
            $this->setEventId($this->getRequest()->getParam('event_id', false));
        }

        public function getLogData() {
            if($this->getEventId()) {
    	        return Mage::getModel('alphamail/event_log')
    	           ->load($this->getEventId());
            } else {
            	throw new Exception("No Event Id given");
            }
        }

        public function getBackUrl() {
            return Mage::helper('adminhtml')->getUrl('*/EventLog');
        }
    }

?>