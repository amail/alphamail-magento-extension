<?php
/**
 * @category    Comfirm
 * @package     Comfirm_AlphaMail
 */
abstract class Comfirm_AlphaMail_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
	protected $_helper = null;
    
	protected function _getHelper(){
		if($this->_helper == null){
			$this->_helper = Mage::helper('alphamail');
		}
		return $this->_helper;
	}

    protected function _initialAction($section, $page, $data = null, $messages = null){
        $this->loadLayout()->_setActiveMenu('system/tools');
        $layout = $this->getLayout();

        $block = $this->getLayout()->createBlock(
            'alphamail/' . $section,
            'alphamail.'.$section.'.'.$page,
            array('template' => 'alphamail/'.$section.'/'.$page.'.phtml')
        );

        // Set default vars
        $block->assign('skin_path', Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN) .
        	"adminhtml/default/default/alphamail");

        if($data != null){
            foreach($data as $key => $value){
                $block->assign($key, $value);
            }
        }

        if($messages != null && count($messages) > 0){
            $session = $this->_getSession();
            
            foreach($messages as $type => $message){
                switch($type){
                    case 'success':
                        $this->_getSession()->addSuccess($this->__($message));
                        break;
                    case 'error':
                        $this->_getSession()->addError($this->__($message));
                        break;
                }
            }

            $this->getLayout()->getMessagesBlock()->addMessages($session->getMessages(true));             
            $this->_initLayoutMessages('core/session');
        }

        $layout->getBlock('content')->append($block);
        $this->renderLayout();
    }

    /*
    error
    Mage::getSingleton(‘core/session’)->addError(‘Custom error here’);
    warning
    Mage::getSingleton(‘core/session’)->addWarning(‘Custom warning here’);
    notice
    Mage::getSingleton(‘core/session’)->addNotice(‘Custom notice here’);
    success
    Mage::getSingleton(‘core/session’)->addSuccess(‘Custom success here’);*/	

    protected function _addSuccessMessage($message){
        $this->_getSession()->addSuccess($this->__($message));
    }

    protected function _addErrorMessage($message){
        $this->_getSession()->addError($this->__($message));
    }
}
