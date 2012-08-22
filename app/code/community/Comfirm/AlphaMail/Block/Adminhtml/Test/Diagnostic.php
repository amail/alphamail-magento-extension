<?php

class Comfirm_AlphaMail_Block_Adminhtml_Test_Diagnostic
    extends Mage_Adminhtml_Block_System_Config_Form_Field {

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $this->setElement($element);
        return $this->_getAddRowButtonHtml($this->__('Run full diagnostic'));
    }
    
    protected function _getAddRowButtonHtml($title) {  
        $buttonBlock = $this->getElement()->getForm()->getParent()->getLayout()
            ->createBlock('adminhtml/widget_button');
        
        $_websiteCode = $buttonBlock->getRequest()->getParam('website');
        
        $params = array(
            'website' => $_websiteCode,
            // We add _store for the base url function, otherwise it will not correctly add the store code if configured
            '_store' => $_websiteCode ? $_websiteCode : Mage::app()->getDefaultStoreView()->getId()
        );
        
        // TODO: for real multi-store self-testing, the test button (and other configuration options)
        // should probably be set to show in website. Currently they are not.
        $url = Mage::helper('adminhtml')->getUrl("alphamail/test/diagnostic", $params);
        
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setLabel($this->__($title))
            ->setOnClick("window.location.href='".$url."'")
            ->toHtml();
    }
}

?>