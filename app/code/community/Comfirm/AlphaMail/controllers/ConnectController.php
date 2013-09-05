<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/tokenservice.class.php");
    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/accountservice.class.php");
    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/captchaservice.class.php");
    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/integrationservice.class.php");

    class Comfirm_AlphaMail_ConnectController extends Comfirm_AlphaMail_Controller_Abstract {
        private $_session;
        private $_captcha_service;

        public function __init(){
            $this->_session = Mage::getSingleton('admin/session');
            $this->_captcha_service = AlphaMailCaptchaService::create();
            $this->_captcha_service->setServiceUrl("http://api.amail.io/v2");
        }

        public function completedAction(){
            $this->__init();
            $this->_initialAction('connect', 'completed', array());
            $this->_session->setAlphaMailRegistrationErrors(null);
        }

        public function loginAction(){
            $this->__init();
            
            $this->_initialAction('connect', 'login', array(
                "username" => "",
                "password" => "",
                "errors" =>  (array)$this->_session->getAlphaMailLoginErrors()
            ));

            $this->_session->setAlphaMailLoginErrors(null);
        }

        public function tryLoginAction(){
            $this->__init();
            $errors = array();
            $parameters = $this->getRequest()->getParams();

            try
            {
                $username = $parameters["username"];
                $password = $parameters["password"];

                $account_service = AlphaMailAccountService::create();
                $account_service->setServiceUrl("http://api.amail.io/v2");

                $result = $account_service->login($username, $password);
                Mage::helper('alphamail/connection')->connect($result->token);

                $this->_redirect('*/*/Completed/');
                return;
            }
            catch(\Exception $exception)
            {
                $errors["password"] = $exception->getMessage();
            }

            $this->_session->setAlphaMailLoginErrors($errors);
            $this->_redirect('*/*/Login/');
        }
        
        public function registerAction(){
            $this->__init();

            $admin_user = $this->_session->getUser();
            $this->_session->setAlphaMailRegistrationErrors(null);

            if(strlen($this->_session->getAlphaMailCaptchaId()) == 0){
                $this->__generateNewCaptcha();
            }

            $this->_initialAction('connect', 'registration', array(
                "fullname" => trim($admin_user->getFirstname() . " " . $admin_user->getLastname()),
                "email" => $admin_user->getEmail(),
                "password" => "",
                "password_repeated" => "",
                "captcha_value" => "",
                "captcha_image_url" => $this->_session->getAlphaMailCaptchaImageUrl(),
                "errors" => array()
            ));
        }

        public function reviewRegistrationAction(){
            $this->__init();

            if(strlen($this->_session->getAlphaMailCaptchaId()) == 0){
                $this->__generateNewCaptcha();
            }

            // Set values
            $fullname = $this->_session->getAlphaMailRegistrationFullname();
            $email = $this->_session->getAlphaMailRegistrationEmail();
            $password = $this->_session->getAlphaMailRegistrationPassword();
            $password_repeated = $this->_session->getAlphaMailRegistrationPasswordRepeated();
            $captcha_value = $this->_session->getAlphaMailRegistrationCaptchaValue();
            $captcha_image_url = $this->_session->getAlphaMailCaptchaImageUrl();
            $errors = (array)$this->_session->getAlphaMailRegistrationErrors();

            // Default values if not set
            if(strlen($fullname) == 0 && strlen($email) == 0 && strlen($password) == 0){
                $admin_user = $this->_session->getUser();
                $email = $admin_user->getEmail();
                $fullname = trim($admin_user->getFirstname() . " " . $admin_user->getLastname());
            }

            $this->_initialAction('connect', 'registration', array(
                "fullname" => $fullname,
                "email" => $email,
                "password" => $password,
                "password_repeated" => $password_repeated,
                "captcha_value" => $captcha_value,
                "captcha_image_url" => $captcha_image_url,
                "errors" => $errors
            ));

            $this->_session->setAlphaMailRegistrationErrors(null);
        }

        public function createAccountAction(){
            // Verify that the request is POST
            if(strtolower($this->getRequest()->getMethod()) != 'post'){
                $this->_redirect('*/*/ReviewRegistration/');
                return;
            }

            $this->__init();
            
            $errors = array();
            $parameters = $this->getRequest()->getParams();
            $email_validator = new Zend_Validate_EmailAddress();

            $fullname = $parameters["fullname"];
            $email = $parameters["email"];
            $password = $parameters["password"];
            $password_repeated = $parameters["password_repeated"];
            $captcha_value = $parameters["captcha_value"];

            // Validate full name
            if(strlen($fullname) == 0){
                $errors["fullname"] = "Full name cannot be empty";
            }

            if(strlen($fullname) != 0 && strlen($fullname) <= 2){
                $errors["fullname"] = "Full name cannot be less than 2 characters";
            }

            // Validate email
            if(strlen($email) == 0){
                $errors["email"] = "Email cannot be empty";
            }

            if(strlen($email) != 0 && !$email_validator->isValid($email)){
                $errors["email"] = "Invalid email address";
            }

            // Validate password
            if(strlen($password) == 0){
                $errors["password"] = "Password cannot be empty";
            }

            if(strlen($password_repeated) == 0){
                $errors["password_repeated"] = "Repeated password cannot be empty";
            }

            if(strlen($password) > 0 && $password != $password_repeated){
                $password_repeated = "";
                $errors["password_repeated"] = "Password doesn't match";
            }

            // Validate captcha value
            if(strlen($captcha_value) == 0){
                $errors["captcha_value"] = "Please verify that you're human by entering the text in the image below";
            }

            // Try and guess the captcha
            if(count($errors) == 0 && !$this->_captcha_service->guess($this->_session->getAlphaMailCaptchaId(), $captcha_value)){
                $captcha_value = "";
                $this->__generateNewCaptcha();
                $errors["captcha_value"] = "Invalid guess. Try guessing the new image below.";
            }

            // No errors, try and process!
            if(count($errors) == 0){
                try
                {
                    $account_service = AlphaMailAccountService::create();
                    $account_service->setServiceUrl("http://api.amail.io/v2");
                    
                    $account_result = $account_service->createNew($email, $fullname, $password, "en", $this->_session->getAlphaMailCaptchaId(), 0);
                    Mage::helper('alphamail/connection')->connect($account_result->token);

                    $this->_redirect('*/*/Completed/');
                    return;
                }
                catch(AlphaMailServiceException $exception)
                {
                    $captcha_value = "";
                    $this->__generateNewCaptcha();
                    $error_message = str_replace("Exception occurred: ", "", $exception->getMessage());
                    Mage::getSingleton('adminhtml/session')->addError("Error: " . $this->__($error_message));
                }
            }

            // Error. Save values and refresh page
            if(count($errors) > 0){
                $this->_session->setAlphaMailRegistrationErrors($errors);
                $this->_session->setAlphaMailRegistrationFullname($fullname);
                $this->_session->setAlphaMailRegistrationEmail($email);
                $this->_session->setAlphaMailRegistrationPassword($password);
                $this->_session->setAlphaMailRegistrationPasswordRepeated($password_repeated);
                $this->_session->setAlphaMailRegistrationCaptchaValue($captcha_value);
            }

            $this->_redirect('*/*/ReviewRegistration/');
        }

        public function renewCaptchaAction(){
            $this->__init();
            $this->__generateNewCaptcha();
            $this->_redirect('*/*/ReviewRegistration/');
        }

        private function __generateNewCaptcha(){
            $captcha = $this->_captcha_service->createNew();
            $this->_session->setAlphaMailCaptchaId($captcha->id);
            $this->_session->setAlphaMailCaptchaImageUrl($captcha->image_url);
            return $captcha;
        }
    }

?>