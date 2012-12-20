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

						$created_projects = array();
						$created_templates = array();

		                $server_url = $helper->getPrimaryServerUrl();
		                $token = $helper->getAuthenticationToken();

		                $project_service = AlphaMailProjectService::create()
		                    ->setServiceUrl($server_url)
		                    ->setApiToken($token);
                    	
		                $template_service = AlphaMailTemplateService::create()
		                    ->setServiceUrl($server_url)
		                    ->setApiToken($token);

                    	foreach($template_service->getAll() as $template){
		                	$created_templates[$template->name] = $template->id;
		                }

		                foreach($project_service->getAll() as $project){
		                	$created_projects[$project->name] = $project->id;
		                }

						foreach ($paths as $path) {
						    if ($path === '.' || $path === '..') continue;

						    $resource_path = $setup_path . '/' . $path;
						    if (is_dir($resource_path)) {
						    	if(file_exists($resource_path . '/data.json')){
						    		$data = json_decode(file_get_contents($resource_path . '/data.json'));

						    		$template_html_version = @file_get_contents($resource_path . '/template.htm');
						    		$template_txt_version = @file_get_contents($resource_path . '/template.txt');

						    		if(!array_key_exists($data->name, $created_templates)){
						    			$created_templates[$data->name] = $template_service->add(
						    				new DetailedTemplate(
						    					null, $data->name,
						    					new TemplateContent(
						    						$template_html_version,
						    						$template_txt_version
					    						)
					    					)
					    				);
						    		}
							    		
							    	if(!array_key_exists($data->name, $created_projects)){
							    		$project_service->add(new DetailedProject(
							    			null, $data->name, $data->subject, 0,
							    			$created_templates[$data->name])
							    		);
							    	}
						    	}
						    }
						}
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
