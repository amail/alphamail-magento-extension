<?php

/* Determines how we should handle disaster moments (in case the primary AM servers cannot be reached) */

class Comfirm_AlphaMail_Model_System_Config_Source_General_Fallback
{
	public function toOptionArray()
	{
		return array(
            "defer"   => Mage::helper('adminhtml')->__('Defer'),
            "discard"   => Mage::helper('adminhtml')->__('Discard (silent)'),
            "exception"   => Mage::helper('adminhtml')->__('Throw exception'),
            "native"   => Mage::helper('adminhtml')->__('Native')
		);
	}
}

?>