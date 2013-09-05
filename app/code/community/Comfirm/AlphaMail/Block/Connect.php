<?php

class Comfirm_AlphaMail_Block_Connect extends Mage_Adminhtml_Block_Template
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('varien/form.js');
        return parent::_prepareLayout();
    }
}

?>