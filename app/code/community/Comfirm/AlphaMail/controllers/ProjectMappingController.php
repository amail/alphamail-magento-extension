<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/projectservice.class.php");

    class Comfirm_AlphaMail_ProjectMappingController extends Comfirm_AlphaMail_Controller_Abstract {
        
    	public function indexAction() {
            try
            {
                $templates = array();
                $this->loadLayout()->_setActiveMenu('system/tools');

                $project_service = AlphaMailProjectService::create()
                    ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                    ->setApiToken($this->_getHelper()->getAuthenticationToken());
               
                $projects = $project_service->getAll();
                $email_templates = $this->_getHelper()->getDefaultCoreTemplates();
                $project_mappings = Mage::getModel('alphamail/Project_Map')->getCollection();

                foreach($email_templates as $template_id => $template){
                    $templates[$template_id] = array('title' => $template['label'], 'selected_id' => -1);
                }

                foreach($project_mappings as $project_map){
                    $project_map = $project_map->getData();
                    if(array_key_exists($project_map['template_name'], $templates)){
                        $am_project_id = (int)$project_map['am_project_id'];
                        if($am_project_id > 0){
                            $templates[$project_map['template_name']]['selected_id'] = $am_project_id;
                        }
                    }
                }

                $this->_initialAction('projectMapping', 'index', array(
                        'templates' => $templates,
                        'projects' => $projects
                    )
                );
            }
            catch(AlphaMailValidationException $exception)
            {
                $this->_addErrorMessage("Validation error: " . $exception->getMessage());
            }
            catch(Exception $exception)
            {
                $this->_initialAction('projectMapping', 'index', null, array('error' => $exception->getMessage()));
            }
        }

        public function saveAction() {
            try
            {
                $changed_mappings = array();
                
                $project_mappings = Mage::getModel('alphamail/Project_Map')->getCollection();
                $template_mappings = $this->_getRequestTemplateMappings();

                // Get all mappings that exist and have changed
                foreach($project_mappings as $project_mapping){
                    $template_id = $project_mapping['template_name'];
                    if(array_key_exists($template_id, $template_mappings)){
                        $am_project_id = $template_mappings[$template_id];
                        if($am_project_id != (int)$template_mappings['am_project_id']){
                            $changed_mappings[$template_id] = array(
                                'id' => (int)$project_mapping['project_map_id'],
                                'am_project_id' => $am_project_id
                            );
                        }
                    }
                }

                // Get all new mappings that does not exist but has been changed
                foreach($template_mappings as $template_id => $am_project_id){
                    if($am_project_id > 0 && !array_key_exists($template_id, $changed_mappings)){
                        $changed_mappings[$template_id] = array(
                            'id' => -1,
                            'am_project_id' => $am_project_id
                        );
                    }
                }

                foreach($changed_mappings as $template_id => $mapping_data){
                    $model = Mage::getModel('alphamail/Project_Map');
                    
                    if($mapping_data['id'] > 0){
                        // Entity exists.. Update!
                        $model->load($mapping_data['id']);
                    }else{
                        // Entity does not exist.. Create!
                        $model->setTemplateName($template_id);
                    }

                    $model->setAmProjectId($mapping_data['am_project_id'])
                        ->save();
                }
            }
            catch(AlphaMailValidationException $exception)
            {
                $this->_addErrorMessage("Validation error: " . $exception->getMessage());
            }
            catch(Exception $exception)
            {
                $this->_addErrorMessage("An error occurred: " . $exception->getMessage());
            }

            $this->_redirectReferer();
        }

        private function _getRequestTemplateMappings(){
            $result = array();

            foreach($this->getRequest()->getParams() as $key=>$value) {
                if($this->_endsWith($key, '_project_id')){
                    $template_id = substr($key, 0, strlen($key)-11);
                    $result[$template_id] = (int)$value;
                }
            }

            return $result;
        }

        private function _endsWith($haystack, $needle)
        {
            $length = strlen($needle);

            if ($length == 0) {
                return true;
            }

            $start  = $length * -1; //negative
            return (substr($haystack, $start) === $needle);
        }
    }

?>