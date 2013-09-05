<?php

    class Comfirm_AlphaMail_Block_SendLog_View extends Mage_Catalog_Block_Product_Abstract {
    	private $_send_id = null;

        public function __construct() {
            parent::__construct();
            $this->setTemplate('alphamail/logging/send_view.phtml');
            $this->setSendId($this->getRequest()->getParam('send_id', false));
        }

        public function getLogData() {
            if($this->getSendId()) {
    	        return Mage::getModel('alphamail/send_log')
    	           ->load($this->getSendId());
            } else {
            	throw new Exception("No Send Id given");
            }
        }

        public function getBackUrl() {
            return Mage::helper('adminhtml')->getUrl('*/sendlog');
        }
    }

?>