<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/emailservice.class.php");

    class Comfirm_AlphaMail_Model_Email_Template extends Mage_Core_Model_Email_Template {

        public function send($email, $name=null, array $variables = array()) {
            $send_log_id = null;
            $helper = Mage::helper('alphamail');
            $is_unhandled = false;
            
            try
            {
                // Plugin is not activated. Pass call to parent.
                if(!$helper->isActivated()){
                     return parent::send($email, $name, $variables);
                }
                
                $template_name = $this->getId();
                $secure = $helper->getSslActivated();
                $subject = $this->getTemplateSubject();
                $token = $helper->getAuthenticationToken();
                $server_url = $helper->getPrimaryServerUrl();
                  
                $project_map_model = Mage::getModel('alphamail/project_map');
                $project_map = $project_map_model->getByTemplateName($template_name);

                if($project_map != null){
                    $project_id = (int)$project_map['am_project_id'];

                    $send_log = $helper->createSendLog($template_name);
                    $send_log_id = $send_log->getId();
                    
                    if($project_id > 0){
                        $message = $this->getMessageObject($template_name);

                        if($message != null){
                            $body_object = $message->load($variables);
                            
                            $name = is_array($name) ? $name[0] : $name;
                            $email = is_array($email) ? $email[0] : $email;

                            // Build payload
                            $payload = EmailMessagePayload::create()
                                ->setProjectId($project_id)
                                ->setSender(new EmailContact($this->getSenderName(), $this->getSenderEmail()))
                                ->setReceiver(new EmailContact($name == null ? "" : $name, $email))
                                ->setBodyObject($body_object);

                            // Attach customer id if set
                            if($body_object != null && $body_object->customer != null && $body_object->customer->customer_id > 0){
                                $payload->setReceiverId($body_object->customer->customer_id);
                            }
                            
                            $max_retries = max($helper->getFailureRetryCount(), 1);
                            $helper->logDebug('Payload = ' . json_encode($payload), $send_log_id);

                            for($retry=$max_retries;$retry>0;--$retry){
                                try
                                {
                                    $email_service = AlphaMailEmailService::create()
                                        ->setServiceUrl($helper->getServerUrl($retry-0))
                                        ->setApiToken($token);

                                    $response = $email_service->queue($payload);
                                    $helper->logDebug('Response = ' . json_encode($response), $send_log_id);

                                    if($response->error_code == 0){
                                        $is_unhandled = false;
                                        $helper->flagSendLogAsSent($send_log, $response->result);
                                        break;
                                    }
                                }
                                // Don't retry validation errors
                                catch (AlphaMailValidationException $exception)
                                {
                                    $helper->logDebug('Validation error = ' . $exception->getMessage(), $send_log_id);
                                    break;
                                }
                                // Don't retry authorization errors
                                catch (AlphaMailAuthorizationException $exception)
                                {
                                    $helper->logDebug('Authorization error = ' . $exception->getMessage(), $send_log_id);
                                    if($retry == 1){
                                        $helper->flagSendLogAsAuthenticationError($send_log, json_encode($payload));
                                    }
                                    break;
                                }
                                // Retry internal errors..
                                catch (AlphaMailInternalException $exception)
                                {
                                    $helper->logDebug('Internal error = ' . $exception->getMessage() . ' (retry ' . ($max_retries-$retry) . ')', $send_log_id);
                                }
                                // Retry service exception..
                                catch (AlphaMailServiceException $exception)
                                {
                                    $helper->logDebug('Service error = ' . $exception->getMessage() . ' (retry ' . ($max_retries-$retry) . ')', $send_log_id);
                                    if($retry == 1){
                                        $helper->flagSendLogAsConnectionError($send_log, json_encode($payload));
                                    } 
                                }
                                // Retry other errors..
                                catch(Exeption $exception)
                                {
                                    $helper->logDebug('Other error = ' . $exception->getMessage() . ' (retry ' . ($max_retries-$retry) . ')', $send_log_id);
                                }
                            }
                        }
                    }
                }
                else
                {
                    $is_unhandled = true;
                    $helper->logDebug("Mail sent with template '" . $template_name . "' was unhandled (not mapped).");
                }
            }
            catch(Exception $exception)
            {
                $is_unhandled = true;
                $helper->logError('Exception thrown when trying to queue mail: ' . $exception->__toString(), $send_log_id);
            }

            try
            {
                if($is_unhandled){
                    switch(Mage::helper('alphamail')->getFallbackMode()){
                        case 'defer':
                            // Save in message log...
                            break;
                        case 'discard':
                            // Just throw away...
                            break;
                        case 'exception':
                            // Throw an exception...
                            break;
                        case 'native':
                            // Let it be handled native
                            return parent::send($email, $name, $variables);
                            break;
                    }
                }
            }
            catch(Exception $exception){
                // Log..
            }

            return true;
        }

        private function getMessageObject($template_name){
            $result = null;

            switch($template_name){
                case 'customer_create_account_email_template':
                case 'customer_create_account_email_confirmed_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Welcome();
                    break;
                case 'customer_create_account_email_confirmation_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Email_Confirmation();
                    break;
                case 'customer_password_forgot_email_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Password_Renewal();
                    break;
                case 'sales_email_order_template':
                case 'sales_email_order_guest_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Sales_Order();
                    break;
                case 'sales_email_order_comment_template':
                case 'sales_email_order_comment_guest_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Sales_Order_Update();
                    break;
                case 'sales_email_invoice_template':
                case 'sales_email_invoice_guest_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Sales_Order_Invoice();
                    break;
                case 'sales_email_invoice_comment_template':
                case 'sales_email_invoice_comment_guest_template':
                    $result = new Comfirm_AlphaMail_Message_Customer_Sales_Order_Invoice_Update();
                    break;
            }

            return $result;
        }
    }

?>