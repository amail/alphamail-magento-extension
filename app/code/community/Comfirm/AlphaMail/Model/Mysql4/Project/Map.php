<?php

class Comfirm_AlphaMail_Model_Mysql4_Project_Map
    extends Mage_Core_Model_Mysql4_Abstract {
    
    protected function _construct() {
        $this->_init('alphamail/project_map', 'project_map_id');
    }
    
    public function getByTemplateName($template_name)
    {
        $adapter = $this->_getReadAdapter();

        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('template_name=:template_name');

        $binds = array('template_name' => $template_name);

        return $adapter->fetchRow($select, $binds);
    }
}

?>