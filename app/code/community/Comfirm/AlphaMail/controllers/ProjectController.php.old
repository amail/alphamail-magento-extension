<?php

include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/projectservice.class.php");
include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/signatureservice.class.php");
include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/templateservice.class.php");

class Comfirm_AlphaMail_ProjectController extends Comfirm_AlphaMail_Controller_Abstract {
    
	public function indexAction() {
        try
        {
            $project_service = AlphaMailProjectService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            $this->_initialAction('project', 'index', array(
                    'projects' => $project_service->getAll()
                )
            );
        }catch(Exception $exception){
            $this->_initialAction('project', 'index', null, array('error' => $exception->getMessage()));
        }
    }
	
    public function editAction() {
        try
        {
            $project_id = (int)$this->getRequest()->getParam('id');

            $project_service = AlphaMailProjectService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            $template_service = AlphaMailTemplateService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            $signature_service = AlphaMailSignatureService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            $this->_initialAction('project', 'edit', array(
                    'project' => $project_service->getSingle($project_id),
                    'signatures' => $signature_service->getAll(),
                    'templates' => $template_service->getAll()
                )
            );
        }catch(Exception $exception){
            $this->_initialAction('project', 'edit', null, array('error' => $exception->getMessage()));
        }
    }

    public function createAction() {
        try
        {
            $template_service = AlphaMailTemplateService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            $signature_service = AlphaMailSignatureService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            $this->_initialAction('project', 'create', array(
                    'signatures' => $signature_service->getAll(),
                    'templates' => $template_service->getAll()
                )
            );
        }catch(Exception $exception){
            $this->_initialAction('project', 'create', null, array('error' => $exception->getMessage()));
        }
    }

    public function saveAction(){        
        try
        {
            $request = $this->getRequest();
            $project_id = (int)$request->getParam('id');

            $project = new DetailedProject(
                $project_id,
                $request->getParam('name'),
                $request->getParam('subject'),
                $request->getParam('signature_id'),
                $request->getParam('template_id')
            );

            $project_service = AlphaMailProjectService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            if($project_id > 0){
                $project_service->update($project);
                $this->_addSuccessMessage("Project successfully updated");
            }else{
                $project_id = $project_service->add($project);
                $this->_addSuccessMessage("Project successfully created");
            }

            $this->_redirectUrl($this->getUrl("*/*/edit", array('id' => $project_id)));
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