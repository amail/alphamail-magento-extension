<?php

    include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/emailservice.class.php");

    class Comfirm_AlphaMail_Model_Email_Template extends Mage_Core_Model_Email_Template {
    	
        public function send($email, $name=null, array $variables = array()) {
            $helper = Mage::helper('alphamail');
            
            try{
                if(!$helper->isActivated()){
                     return parent::send($email, $name, $variables);
                }
                
                $template_name = $this->getId();
                $secure = $helper->getSslActivated();
                $subject = $this->getTemplateSubject();
                $token = $helper->getAuthenticationToken();
                $server_url = $helper->getPrimaryServerUrl();

                // If payload name is set, replace original name with this.
                if($name == null || strlen($payload->name) == 0){
                    if($payload->name != null){
                        $name = $payload->name;
                    }
                }

                $project_map_model = Mage::getModel('alphamail/project_map');
                $project_map = $project_map_model->getByTemplateName($template_name);

                if($project_map != null){
                    $send_id = null;
                    $project_id = (int)$project_map['am_project_id'];
                    if($project_id > 0){
                        $message = null;

                        switch($template_name){
                            case 'customer_create_account_email_template':
                            case 'customer_create_account_email_confirmed_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Welcome();
                                break;
                            case 'customer_create_account_email_confirmation_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Email_Confirmation();
                                break;
                            case 'customer_password_forgot_email_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Password_Renewal();
                                break;
                            case 'sales_email_order_template':
                            case 'sales_email_order_guest_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Sales_Order();
                                break;
                            case 'sales_email_order_comment_template':
                            case 'sales_email_order_comment_guest_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Sales_Order_Update();
                                break;
                            case 'sales_email_invoice_template':
                            case 'sales_email_invoice_guest_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Sales_Order_Invoice();
                                break;
                            case 'sales_email_invoice_comment_template':
                            case 'sales_email_invoice_comment_guest_template':
                                $message = new Comfirm_AlphaMail_Message_Customer_Sales_Order_Invoice_Update();
                                break;
                        }

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
                            
                            $helper->logDebug(json_encode($body_object));
                            $helper->logDebug('Payload built. Data = ' . json_encode($payload) . '.', $send_id);

                            for($retry=max($helper->getFailureRetryCount(), 1);$retry>0;--$retry){
                                try
                                {
                                    $email_service = AlphaMailEmailService::create()
                                        ->setServiceUrl($helper->getServerUrl($retry-0))
                                        ->setApiToken($token);

                                    $response = $email_service->queue($payload);

                                    $helper->logDebug('Response = ' . json_encode($response));

                                    if($response->error_code == 0){
                                        $helper->logSentMessage($response->result);
                                        $helper->logDebug('Request successful. Message = \'' . $response->message . '\', Id = \'' . $response->result . '\'', $send_id);
                                        $is_handled = true;

                                        // Successful! Let's end here :)
                                        break;
                                    }
                                }
                                catch (AlphaMailValidationException $exception)
                                {
                                    // Don't retry validation errors
                                    $helper->logDebug('Validation error = ' . $exception->getMessage());
                                    $is_handled = true;
                                    break;
                                }
                                catch (AlphaMailAuthorizationException $exception)
                                {
                                    // Don't retry authorization errors
                                    $helper->logDebug('Authorization error = ' . $exception->getMessage());
                                    $is_handled = true;
                                    break;
                                }
                                catch (AlphaMailInternalException $exception)
                                {
                                    // Retry internal errors..
                                    $helper->logDebug('Internal error = ' . $exception->getMessage());
                                }
                                catch (AlphaMailServiceException $exception)
                                {
                                    // Retry service exception..
                                    $helper->logDebug('Service error = ' . $exception->getMessage());
                                }
                                catch(Exeption $exception)
                                {
                                    // Retry other errors..
                                    $helper->logDebug('Other error = ' . $exception->getMessage());
                                }
                            }
                        }
                    }
                }else{
                    $helper->logDebug("Mail sent with template '" . $template_name . "' was unhandled (not mapped).");
                }
            }catch(Exception $exception){
                $is_handled = false;
                // TODO: LOG THIS EXCEPTION!!!
                //Mage::logException($exception);
                //$helper->logError('Exception thrown when trying to queue mail: ' . $exception->__toString(), $send_id);
            }

            try
            {
                if(!$is_handled){
                    switch(Mage::helper('alphamail')->getFallbackMode()){
                        case 'defer':
                            // Save in message log..
                            break;
                        case 'discard':
                            // Just throw away..
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
    }

?>