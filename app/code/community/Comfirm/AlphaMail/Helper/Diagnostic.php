<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/signatureservice.class.php");

    class Comfirm_AlphaMail_Helper_Diagnostic extends Mage_Core_Helper_Abstract {

        private $_authentication_lookup_cache = array();

        public function getDiagnosticError(){
            $error = null;
            $helper = Mage::helper('alphamail');

            // Validate configuration..
            if($error == null){
                $server_token = $helper->getAuthenticationToken();
                $server_address = $helper->getPrimaryServerAddress();
                if(empty($server_address)){
                    $error = "Primary server address is empty. In order to use this extension you need to enter a correct AlphaMail service address e.g. 'api.amail.io'.";
                }else if(empty($server_token)){
                    $error = "Authentication token is empty. Please enter a valid authentication token and try again.";
                }
            }

            // Test for model rewrite order
            if($error == null){
                $current_email_template_rewrite = get_class(Mage::getModel('core/email_template'));
                if($current_email_template_rewrite != 'Comfirm_AlphaMail_Model_Email_Template'){
                    $error = "The AlphaMail extension is not the first extension rewriting 'core/email_template'. The first model is currently '" . $current_email_template_rewrite . "'.<br />" .
                        "This may be due to other email extensions being installed. You need to uninstall the other extensions for the AlphaMail extension to function properly.";
                }
            }

            // Validate that if SSL is turned on then the OpenSSL extension is installed
            if($error == null){
                if($helper->getSslActivated() && !function_exists('openssl_verify')){
                    $error = "Secure connection (SSL) enabled but OpenSSL is not installed. OpenSSL must be installed for this to function.";
                }
            }

            // Validate that there is no firewall issues / problem connecting to the API server
            if($error == null){
                $target_port = $helper->getSslActivated() ? 443 : 80;
                if(!$this->canConnectToSocket($helper->getPrimaryServerAddress(), $target_port)){
                    $error = "Unable to access API server '" . $helper->getPrimaryServerAddress() . "' on port '" . $target_port . "'! Please check that '" .
                        $helper->getPrimaryServerAddress() . ":" . $target_port . "' is allowed for outgoing connection in your firewall!";
                }
            }

            // Validate that the user token authenticates successfully
            if($error == null){
                list($error_code, $message) = $this->tryAuthenticateWithToken(
                    $helper->getPrimaryServerUrl(), $helper->getAuthenticationToken());

                if($error_code == -1){
                    $error = "Failed trying to authenticate with token. Please validate that the token is typed in correctly and activated.";
                }else if($error_code < -1){
                    $error = $message;
                }
            }

            // Validate that all projects belong to this account

            return $error;
        }

        public function tryAuthenticateWithToken($server_url, $token){
            $result = null;
            $checksum = md5(md5($server_url) . md5($token));

            if(array_key_exists($checksum, $this->_authentication_lookup_cache)){
                return $this->_authentication_lookup_cache[$checksum];
            }

            try
            {
                AlphaMailSignatureService::create()
                    ->setServiceUrl($server_url)
                    ->setApiToken($token)
                    ->getAll();

                // Need to cache the authentication here

                $result = array(0, 'Authentication was successful, token is valid!');
            }
            catch(AlphaMailAuthorizationException $exception)
            {
                $result = array(-1, "Invalid token.");
            }
            catch(Exception $exception)
            {
                $result = array(-2, $exception->getMessage());
            }

            return $this->_authentication_lookup_cache[$checksum] = $result;
        }

        private function canConnectToSocket($address, $port){
            $responding = false;
            $handle = @fsockopen($address, $port);

            if(is_resource($handle)){
                $responding = true;
                @fclose($handle);
            }

            return $responding;
        }

    }

?>