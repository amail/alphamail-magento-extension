<?php

class Comfirm_AlphaMail_EventLogController
	extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
            ->_setActiveMenu('system/tools')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'))
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Email Event Log'), Mage::helper('adminhtml')->__('Event Log'));
            
        return $this;
    }
    
	public function indexAction() {	
		  $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('alphamail/EventLog'))
            ->renderLayout();
	}
	
	public function viewAction() {
		  $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('alphamail/EventLog_View'))
            ->renderLayout();
	}
} 

?>