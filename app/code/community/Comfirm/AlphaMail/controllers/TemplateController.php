<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/templateservice.class.php");

    class Comfirm_AlphaMail_TemplateController extends Comfirm_AlphaMail_Controller_Abstract {

        public function indexAction() {
            try {
                $template_service = AlphaMailTemplateService::create()
                    ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                    ->setApiToken($this->_getHelper()->getAuthenticationToken());

                $this->_initialAction('template', 'index', array(
                        'templates' => $template_service->getAll()
                    )
                );
            }catch(Exception $exception){
                $this->_initialAction('template', 'index', null, array('error' => $exception->getMessage()));
            }
        }
        
        public function editAction() {
            try {
                $template_id = (int)$this->getRequest()->getParam('id');

                $template_service = AlphaMailTemplateService::create()
                    ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                    ->setApiToken($this->_getHelper()->getAuthenticationToken());

                $this->_initialAction('template', 'edit', array(
                        'template' => $template_service->getSingle($template_id)
                    )
                );
            }catch(Exception $exception){
                $this->_initialAction('template', 'index', null, array('error' => $exception->getMessage()));
            }
        }

        public function createAction() {
            $this->_initialAction('template', 'create');
        }

        public function saveAction(){
            try
            {
                $request = $this->getRequest();
                $template_id = (int)$request->getParam('id');
                
                $template = new DetailedTemplate(
                    $template_id,
                    $request->getParam('name'),
                    new TemplateContent(
                        $request->getParam('html_version'),
                        $request->getParam('text_version')
                    )
                );

                $template_service = AlphaMailTemplateService::create()
                    ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                    ->setApiToken($this->_getHelper()->getAuthenticationToken());

                if($template_id > 0){
                    $template_service->update($template);
                    $this->_addSuccessMessage("Template successfully updated");
                }else{
                    $template_id = $template_service->add($template);
                    $this->_addSuccessMessage("Template successfully created");
                }

                $this->_redirectUrl($this->getUrl("*/*/edit", array('id' => $template_id)));
            }
            catch(AlphaMailValidationException $exception)
            {
                $this->_addErrorMessage("Validation error: " . $exception->getMessage());
                $this->_redirectReferer();
            }
            catch(Exception $exception)
            {
                $this->_redirectUrl($this->getUrl("*/*/index"));
            }
        }
    }

?>
