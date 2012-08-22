<?php

class Comfirm_AlphaMail_Model_System_Config_Source_General_Debug
{
    public function toOptionArray()
    {
        return array(
        	"disabled"   => Mage::helper('adminhtml')->__('Disabled'),
            "contact"   => Mage::helper('adminhtml')->__('Redirect to contact form email'),
            "supress"   => Mage::helper('adminhtml')->__('Supress all emails')
        );
    }
}

?>