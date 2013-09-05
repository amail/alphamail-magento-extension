<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/accountservice.class.php");
    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/integrationservice.class.php");
    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/tokenservice.class.php");

    class Comfirm_AlphaMail_Helper_Connection extends Mage_Core_Helper_Abstract {
        public function connect($token_value, $installation_id = "Magento"){
            $token_id = null;
            $token_exists = false;
            $integration_id = null;
            $integration_exists = false;
             
            $token_service = AlphaMailTokenService::create();
            $token_service->setServiceUrl("http://api.amail.io/v2");
            $token_service->setApiToken($token_value);

            // Scan for the Magento token
            foreach($token_service->getAll() as $token){
                if($token->name == $installation_id){
                    $token_exists = true;
                    $token_id = $token->id;
                    $token_value = $token_service->getSingle($token->id)->token;
                    break;
                }
            }

            // Create the token if it doesn't exist
            if(!$token_exists){
                $magento_token = $token_service->createNew($installation_id, true);
                $token_id = $token->id;
                $token_value = $magento_token->token;
            }

            $integration_service = AlphaMailIntegrationService::create();
            $integration_service->setServiceUrl("http://api.amail.io/v2");
            $integration_service->setApiToken($token_value);

            // Scan for the Magento integration
            foreach($integration_service->getAll() as $integration){
                if($integration->name == $installation_id){
                    $integration_exists = true;
                    $integration_id = $integration->id;
                    break;
                }
            }

            // Create the integration if it doesn't exist
            if(!$integration_exists){
                $integration = $integration_service->createNew($installation_id, array(
                    "url" => Mage::getBaseUrl(),
                    "version" => Mage::getVersion(),
                    "os" => php_uname()
                ));

                $integration_id = $integration->id;
            }

            $integration_service->connectToken($integration_id, $token_id);
            Mage::helper('alphamail')->setAuthenticationToken($token_value);

            // Clean tables for left over data
            Mage::helper('alphamail')->truncateTableData();
        }
    }

?>