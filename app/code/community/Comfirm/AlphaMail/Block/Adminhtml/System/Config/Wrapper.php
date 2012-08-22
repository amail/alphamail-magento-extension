<?php

	class Comfirm_AlphaMail_Block_Adminhtml_System_Config_Wrapper extends Mage_Adminhtml_Block_Template
	{
		public function __construct(){
			parent::__construct();
			$helper = Mage::helper('alphamail');
			if($this->isAlphaMailSection() && $helper->isActivated()){
				$session = Mage::getSingleton('adminhtml/session');
				$diagnostic_error = Mage::helper('alphamail/diagnostic')->getDiagnosticError();

				if($diagnostic_error == null){
					if($helper->getConfigKey("authentication/last_validated_token_checksum") != null){
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
