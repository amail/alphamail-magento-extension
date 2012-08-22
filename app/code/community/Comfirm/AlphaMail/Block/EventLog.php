<?php

class Comfirm_AlphaMail_Block_EventLog
    extends Mage_Adminhtml_Block_Widget_Grid_Container {

    public function __construct() {
    	$this->_blockGroup = 'alphamail';
        $this->_controller = 'EventLog';
        $this->_headerText = Mage::helper('cms')->__('Event Log');
        parent::__construct();
        
        // Remove the add button
        $this->_removeButton('add');
    }
}

?>