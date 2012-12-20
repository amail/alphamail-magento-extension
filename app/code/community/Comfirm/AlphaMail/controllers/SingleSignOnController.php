<?php

	include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/userticketservice.class.php");

    class Comfirm_AlphaMail_SingleSignOnController extends Comfirm_AlphaMail_Controller_Abstract {
        public function indexAction() {
            $this->showSingleSignOn();
		}

        public function tokensAction(){
            $this->showSingleSignOn("/tokens/");
        }

        public function signaturesAction(){
            $this->showSingleSignOn("/signatures/");
        }

        public function settingsAction(){
            $this->showSingleSignOn("/settings/");
        }

        private function showSingleSignOn($path = null){
            $ticket_service = AlphaMailUserTicketService::create()
                ->setServiceUrl($this->_getHelper()->getPrimaryServerUrl())
                ->setApiToken($this->_getHelper()->getAuthenticationToken());

            try
            {
                $ticket = $ticket_service->createNew(new CreateUserTicket(3600, $path));
                $this->_initialAction('singleSignOn', 'index', array(
                        'url' => isset($ticket) ? $ticket->sso_url : null
                    )
                );
            }
            catch(Exception $exception){
                $this->_initialAction('singleSignOn', 'index', null, array('error' => $exception->getMessage()));
            }
        }
	}

?>