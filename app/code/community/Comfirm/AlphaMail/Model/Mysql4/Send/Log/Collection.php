<?php

class Comfirm_AlphaMail_Model_Mysql4_Send_Log_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	
    protected function _construct() {
        $this->_init('alphamail/send_log');
    }
}