<?php

    class Comfirm_AlphaMail_Block_EventLog extends Mage_Adminhtml_Block_Widget_Grid_Container {
        public function __construct() {
            parent::__construct();
            $send_id = $this->getRequest()->getParam('send_id', false);

        	$this->_blockGroup = 'alphamail';
            $this->_controller = 'eventLog';

            if($send_id == null){
                $this->_headerText = Mage::helper('cms')->__('Event Log');
            }else{
                $this->_headerText = Mage::helper('cms')->__('Event Log (filtering by send id ' . $send_id . ')');
            }
            
            // Remove the add button
            $this->_removeButton('add');

        }
    }

?>