<?php
/**
 * @author Ashley Schroder (aschroder.com)
 * @copyright  Copyright (c) 2010 Ashley Schroder
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Comfirm_AlphaMail_Block_SendLog extends Mage_Adminhtml_Block_Widget_Grid_Container {
	
    /**
     * Block constructor
     */
    public function __construct() {
    	$this->_blockGroup = 'alphamail';
        $this->_controller = 'sendLog';
        $this->_headerText = Mage::helper('cms')->__('Send Log');
        parent::__construct();
        
        // Remove the add button
        $this->_removeButton('add');
    }

}
