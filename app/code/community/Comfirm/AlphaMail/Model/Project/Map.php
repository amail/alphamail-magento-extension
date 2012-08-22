<?php

class Comfirm_AlphaMail_Model_Project_Map extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('alphamail/project_map');
    }
    
    public function getByTemplateName($template_name){
        return $this->_getResource()->getByTemplateName($template_name);
    }
}

?>