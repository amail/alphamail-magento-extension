<?php

class Comfirm_AlphaMail_Helper_Data extends Mage_Core_Helper_Abstract {

    // Event
    const EVENT_INFO = 0;
    const EVENT_ERROR = 1;
    const EVENT_DEBUG = 2;

    // Send
    const SEND_QUEUED = 0;
    const SEND_AUTHENTICATION_ERROR = 1;
    const SEND_CONNECTION_ERROR = 2;
    const SEND_SENT = 3;

	public function isActivated(){
		return (bool)$this->getConfigKey('general/activated') &&
            !Mage::getStoreConfigFlag('advanced/modules_disable_output/Comfirm_AlphaMail');
	}

    public function getAuthenticationToken(){
        return $this->getConfigKey('authentication/token');
    }

    public function setAuthenticationToken($token){
        return $this->setConfigKey('authentication/token', $token);
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

    public function truncateTableData(){
        // Remove project map
        foreach(Mage::getModel('alphamail/project_map')->getCollection() as $log){
            $log->delete();
        }
        // Remove logs
        foreach(Mage::getModel('alphamail/event_log')->getCollection() as $log){
            $log->delete();
        }
        foreach(Mage::getModel('alphamail/send_log')->getCollection() as $log){
            $log->delete();
        }
    }

    public function getConfigKey($path){
        return Mage::getStoreConfig('alphamail/' . $path);
    }
    
    public function getDefaultCoreTemplates(){
        return Mage_Core_Model_Email_Template::getDefaultTemplates();
    }

    // Logging

    // Event logs

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
        $this->logEvent($send_id, $message, self::EVENT_INFO);
        return $this;
    }

    public function logError($message, $send_id = null) {
        $this->logEvent($send_id, $message, self::EVENT_ERROR);
        return $this;
    }

    public function logDebug($message, $send_id = null) {
        if($this->isDebuggingEnabled()){
            $this->logEvent($send_id, $message, self::EVENT_DEBUG);
        }
        return $this;
    }

    // Send logs

    public function createSendLog($template_name){
        return Mage::getModel('alphamail/send_log')
            ->setTemplateName($template_name)
            ->setCreatedAt(time())
            ->save();
    }

    public function flagSendLogAsSent($send_log, $am_queue_id){
        return $send_log->setStatus(self::SEND_SENT)
            ->setAmQueueId($am_queue_id)
            ->setRawPayload(null)
            ->setSentAt(time())
            ->save();
    }

    public function flagSendLogAsQueued($send_log, $payload){
        return $send_log->setStatus(self::SEND_QUEUED)
            ->setRawPayload($payload)
            ->save();
    }

    public function flagSendLogAsConnectionError($send_log, $payload){
        return $send_log->setStatus(self::SEND_CONNECTION_ERROR)
            ->setRawPayload($payload)
            ->save();
    }

    public function flagSendLogAsAuthenticationError($send_log, $payload){
        return $send_log->setStatus(self::SEND_AUTHENTICATION_ERROR)
            ->setRawPayload($payload)
            ->save();
    }

    public function logSentMessage($am_queue_id) {
        Mage::getModel('alphamail/send_log')
            ->setAmQueueId($am_queue_id)
            ->save();

        return $this;
    }
}
