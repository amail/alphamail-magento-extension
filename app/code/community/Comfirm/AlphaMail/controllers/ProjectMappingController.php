<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/projectservice.class.php");

    class Comfirm_AlphaMail_ProjectMappingController extends Comfirm_AlphaMail_Controller_Abstract {

        const STATE_UNMODIFIED = 0;
        const STATE_MODIFIED = 1;
        const STATE_NOT_CREATED = 2;
        const STATE_DELETED = 3;

        private static $_messages = array(
            "customer-created" => array(
                "title" => "Customer Created",
                "templates" => array(
                    "customer_create_account_email_template",
                    "customer_create_account_email_confirmed_template"
                )
            ),
            "customer-verify-email" => array(
                "title" => "Customer Verify Email",
                "templates" => array(
                    "customer_create_account_email_confirmation_template"
                )
            ),
            "customer-reset-password" => array(
                "title" => "Customer Reset Password",
                "templates" => array(
                    "customer_password_forgot_email_template"
                )
            ),
            "order-created" => array(
                "title" => "Order Created",
                "templates" => array(
                    "sales_email_order_template",
                    "sales_email_order_guest_template"
                )
            ),
            "order-updated" => array(
                "title" => "Order Updated",
                "templates" => array(
                    "sales_email_order_comment_template",
                    "sales_email_order_comment_guest_template"
                )
            ),
            "invoice-created" => array(
                "title" => "Invoice Created",
                "templates" => array(
                    "sales_email_invoice_template",
                    "sales_email_invoice_guest_template"
                )
            ),
            "invoice-updated" => array(
                "title" => "Invoice Created",
                "templates" => array(
                    "sales_email_invoice_comment_template",
                    "sales_email_invoice_comment_guest_template"
                )
            )
        );
        
    	public function indexAction() {
            try
            {
                $this->loadLayout()->_setActiveMenu('system/tools');

                $project_service = AlphaMailProjectService::create()
                    ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                    ->setApiToken($this->_getHelper()->getAuthenticationToken());
                
                $projects = array();
                $messages = self::$_messages;
                $project_mappings = Mage::getModel('alphamail/Project_Map')->getCollection();

                foreach($project_service->getAll() as $project){
                    $projects[(int)$project->id] = $project;
                }

                foreach($messages as $message_id => $message){
                    $messages[$message_id]["am_project_id"] = null;
                }

                foreach($project_mappings as $project_map){
                    $project_map = $project_map->getData();
                    foreach($messages as $message_id => $message){
                        if(in_array($project_map['template_name'], $message["templates"])){
                            $am_project_id = (int)$project_map['am_project_id'];
                            if(array_key_exists($am_project_id, $projects)){
                                $messages[$message_id]["am_project_id"] = $am_project_id;
                                break;
                            }
                        }
                    }
                }

                $this->_initialAction('projectMapping', 'index', array(
                        'messages' => $messages,
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
            $changes = array();
            $existing_map = array();
            $template_mappings = $this->_getRequestTemplateMappings();

            foreach(Mage::getModel('alphamail/Project_Map')->getCollection() as $item){
                $data = $item->getData();
                $existing_map[$data["template_name"]] = array(
                    "project_map_id" => (int)$data["project_map_id"],
                    "am_project_id" => (int)$data["am_project_id"],
                    "state" => self::STATE_UNMODIFIED
                );
            }

            foreach($template_mappings as $template_name => $project_id){
                $project_id = (int)$project_id;
                if(array_key_exists($template_name, $existing_map)){
                    $changes[$template_name] = array(
                        "project_map_id" => $existing_map[$template_name]["project_map_id"],
                        "am_project_id" => $project_id,
                        "state" => $existing_map[$template_name]["am_project_id"] != $project_id
                            ? self::STATE_MODIFIED : self::STATE_UNMODIFIED
                    );
                }else{
                    $changes[$template_name] = array(
                        "am_project_id" => $project_id,
                        "state" => self::STATE_NOT_CREATED
                    );
                }
            }

            foreach($existing_map as $template_name => $data){
                if(!array_key_exists($template_name, $template_mappings)){
                    $data["state"] = self::STATE_DELETED;
                    $changes[$template_name] = $data;
                }
            }

            foreach($changes as $template_name => $change){
                $state = $change["state"];

                if($state == self::STATE_UNMODIFIED){
                    continue; // NOP
                }

                $model = Mage::getModel('alphamail/Project_Map');

                if($state != self::STATE_NOT_CREATED){
                    $model->setId($change["project_map_id"]);
                }

                if($state == self::STATE_DELETED){
                    $model->delete();
                }else{
                    $model->setAmProjectId($change["am_project_id"]);
                    $model->setTemplateName($template_name);
                    $model->save();
                }
            }

            $this->_redirectReferer();
        }

        private function _getRequestTemplateMappings(){
            $result = array();

            $parameters = $this->getRequest()->getParams();
            $mappings = json_decode($parameters["mappings"]);

            foreach($mappings as $message_name => $project_id){
                foreach(self::$_messages[$message_name]["templates"] as $template_name){
                    $result[$template_name] = (int)$project_id;
                }
            }

            return $result;
        } 
    }

?>