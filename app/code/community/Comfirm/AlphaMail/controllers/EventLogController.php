<?php

    class Comfirm_AlphaMail_EventLogController
    	extends Mage_Adminhtml_Controller_Action {

        protected function _initAction() {
            $this->loadLayout()->_setActiveMenu('system/tools');
            return $this;
        }	
    		
    	public function indexAction() {
            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('alphamail/eventLog'))
                ->renderLayout();
    	}	
    	
    	public function viewAction() {
            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('alphamail/eventLog_view'))
                ->renderLayout();
    	}

        public function removeAllAction(){
            foreach(Mage::getModel('alphamail/event_log')->getCollection() as $item){
                $item->delete();
            }

            $this->_redirect('*/EventLog/');
        }
    }

?>