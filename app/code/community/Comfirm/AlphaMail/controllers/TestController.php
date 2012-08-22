<?php

include_once(Mage::getBaseDir() . "/app/code/community/Comfirm/AlphaMail/libraries/comfirm.alphamail.client/signatureservice.class.php");

class Comfirm_AlphaMail_TestController
	extends Mage_Adminhtml_Controller_Action {

    public function sendAction() {
        Mage::log("Complete");

        if(true) {
            $msg = "Testing sending single email... Successful!";
            Mage::getSingleton('adminhtml/session')->addSuccess($msg);
        }else{
            $msg = "Testing failed";
            Mage::getSingleton('adminhtml/session')->addError($msg);
        }
        
        $this->_redirectReferer();
    }

    public function diagnosticAction() {
        $session = Mage::getSingleton('adminhtml/session');

        $diagnostic_error = Mage::helper('alphamail/diagnostic')->getDiagnosticError();

        if($diagnostic_error != null){
            $session->addError("Diagnostic error: " . $diagnostic_error);
        }else{
            $session->addSuccess("Full diagnostic ran successfully. No errors could be found.");
        }

        $this->_redirectReferer();
    }
} 

?>