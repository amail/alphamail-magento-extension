<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/projectservice.class.php");
    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/templateservice.class.php");

	class Comfirm_AlphaMail_Block_Adminhtml_System_Config_Wrapper extends Mage_Adminhtml_Block_Template
	{
		public function __construct(){
			parent::__construct();
			$helper = Mage::helper('alphamail');
			$diagnostic_helper = Mage::helper('alphamail/diagnostic');
			if($this->isAlphaMailSection() && $helper->isActivated()){
				$session = Mage::getSingleton('adminhtml/session');
				$diagnostic_error = $diagnostic_helper->getDiagnosticError();

				$setup_path = Mage::getBaseDir('code') . '/community/Comfirm/AlphaMail/data/templates/';
				$paths = scandir($setup_path);

				if($diagnostic_error == null){
					$token_checksum = hash("sha256", $helper->getAuthenticationToken());
					if($helper->getConfigKey("authentication/last_validated_token_checksum") != $token_checksum){
						$helper->setConfigKey('authentication/last_validated_token_checksum', $token_checksum);
						Mage::helper('alphamail/connection')->connect($helper->getAuthenticationToken());
					}
				}else{
					$session->addError("Error: " . $diagnostic_error);
				}
			}
		}

		public function isAlphaMailSection()
		{
			return strtolower(trim($this->getParam('section'))) == 'alphamail';
		}
		
		public function getSession()
		{
			return Mage::getSingleton('adminhtml/session');
		}
		
		public function getParam($param, $default = null)
		{
			return Mage::app()->getRequest()->getParam($param, $default);
		}
		
		public function getModuleName()
		{
			return parent::getModuleName() . '_Adminhtml';
		}
	}

?>
