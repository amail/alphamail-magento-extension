<?php

    class Comfirm_AlphaMail_SendLogController
    	extends Mage_Adminhtml_Controller_Action {

        protected function _initAction() {
            // load layout, set active menu and breadcrumbs
            $this->loadLayout()
                ->_setActiveMenu('system/tools');
            return $this;
        }	
    		
    	public function indexAction() {
            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('alphamail/sendLog'))
                ->renderLayout();
    	}	
    	
    	public function viewAction() {
            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock('alphamail/sendLog_view'))
                ->renderLayout();
    	}

        public function removeAllAction(){
            foreach(Mage::getModel('alphamail/send_log')->getCollection() as $item){
                $item->delete();
            }

            $this->_redirect('*/SendLog/');
        }
    }

?>