<?php

class Comfirm_AlphaMail_Helper_Data extends Mage_Core_Helper_Abstract {

	public function isActivated(){
		return (bool)$this->getConfigKey('general/activated') &&
            !Mage::getStoreConfigFlag('advanced/modules_disable_output/Comfirm_AlphaMail');
	}

    public function getAuthenticationToken(){
        return $this->getConfigKey('authentication/token');
    }

    public function isLoggingEnabled() {
        return (bool)$this->getConfigKey('debugging/logging_mode');
    }

    public function isDebuggingEnabled() {
        return (bool)$this->getConfigKey('debugging/debug_mode');
    }

    public function getSslActivated(){
        return (bool)$this->getConfigKey('authentication/ssl');
    }

    public function getPrimaryServerAddress(){
        return $this->getServerAddress(0);
    }

    public function getServerUrl($offset = 0){
        return $this->getAddressUrl($this->getServerAddress($offset));
    }

    public function getServerAddress($offset = 0){
        $address_data = $this->getConfigKey('system/primary_address');
        $addresses = explode(",", $address_data);

        foreach($addresses as $key => $value){
            $value = trim($value);

            if(empty($value)){
                unset($addresses[$key]);
            }else{
                $addresses[$key] = $value;
            }
        }

        if(count($addresses) == 0){
            return null;
        }

        return $addresses[$offset % count($addresses)];
    }

    private function getAddressUrl($address){
        return ($this->getSslActivated() ? "https" : "http") . "://" . $address . "/v1";
    }

    public function getPrimaryServerUrl(){
        return $this->getServerUrl(0);
    }

    public function getFallbackMode(){
        return $this->getConfigKey('system/fallback_mode');
    }

    public function getFailureRetryCount(){
        return (int)$this->getConfigKey('system/number_of_retries');
    }

    public function setConfigKey($path, $value){
        $config = Mage::getConfig();
        
        // Save config and refresh stores
        $config->saveConfig("alphamail/" . $path, $value);
        $config->reinit();
        Mage::app()->reinitStores();

        return $this;
    }

    public function getConfigKey($path){
        return Mage::getStoreConfig('alphamail/' . $path);
    }
    
    public function getDefaultCoreTemplates(){
        return Mage_Core_Model_Email_Template::getDefaultTemplates();
    }

    public function logEvent($send_id, $message, $type) {
        if($this->isLoggingEnabled()){
            if(strlen($message) >= 4096){
                $message = substr($message, 0, 4096);
            }

            Mage::getModel('alphamail/event_log')
                ->setSendId($send_id)
                ->setMessage($message)
                ->setType($type)
                ->save();
        }
        return $this;
    }

    public function logInformation($message, $send_id = null) {
        $this->logEvent($send_id, $message, 0);
        return $this;
    }

    public function logError($message, $send_id = null) {
        $this->logEvent($send_id, $message, 1);
        return $this;
    }

    public function logDebug($message, $send_id = null) {
        if($this->isDebuggingEnabled()){
            $this->logEvent($send_id, $message, 2);
        }
        return $this;
    }

    public function logSentMessage($am_queue_id) {
        Mage::getModel('alphamail/send_log')
            ->setAmQueueId($am_queue_id)
            ->save();

        return $this;
    }
}
