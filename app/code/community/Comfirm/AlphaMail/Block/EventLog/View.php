<?php

class Comfirm_AlphaMail_Block_EventLog_View extends Mage_Catalog_Block_Product_Abstract {
	 
    public function __construct() {
        parent::__construct();
        $this->setTemplate('alphamail/view.phtml');
        $this->setEmailId($this->getRequest()->getParam('event_id', false));
    }

    public function getEmailData() {
        if( $this->getEmailId()) {
	        return Mage::getModel('alphamail/event_log')
			    ->load($this->getEmailId());
        } else {
        	throw new Exception("No Event Id given");
        }
    }

    public function getBackUrl() {
        return Mage::helper('adminhtml')->getUrl('*/eventlog');
    }
    
    // This is an experiment in progress - if you're 
    // reading this code, maybe you'd be interested...
    
    // The idea being that during development we can include 
    // templates from the actual extension code, rather than rely on them 
    // coming from ... not entriely convinced it's a good thing...
    
    public function fetchView($fileName) {
    	
    	// This is so we do not need to keep templates 
    	// outside of the extension code.
    	$class_parts = explode("_", __CLASS__);
    	$dev_template_path = Mage::getModuleDir('', $class_parts[0]."_".$class_parts[1]).DS."templates";
    	
    	if(Mage::getIsDeveloperMode() && 
    		file_exists($dev_template_path.DS.$fileName)) {
    			
    		Mage::log("NOTE: Loading template from development path - not the design directory");
	    	$this->setScriptPath($dev_template_path);
    	} else {
    		$this->setScriptPath(Mage::getBaseDir('design'));
    	}
    	
    	return parent::fetchView($fileName);
    }

}

?>