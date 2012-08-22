<?php

class Comfirm_AlphaMail_Model_Mysql4_Event_Log extends Mage_Core_Model_Mysql4_Abstract {
    /**
     * Resource model initialization
     */
    protected function _construct() {
        $this->_init('alphamail/event_log', 'event_id');
    }
}