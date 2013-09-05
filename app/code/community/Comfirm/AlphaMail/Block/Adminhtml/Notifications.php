<?php

    class Comfirm_AlphaMail_Block_Adminhtml_Notifications extends Mage_Core_Block_Text
    {
        protected function _toHtml()
        {
    		$html = "";
    		
            $admin_helper = Mage::helper("adminhtml");  
            $helper = Mage::helper('alphamail');

            if(strlen($helper->getAuthenticationToken()) == 0) {
                $html = "<div class='notification-global'>";
            	$html .= "The AlphaMail module requires a AlphaMail account. ";
                $html .= "<a href='" . $admin_helper->getUrl("alphamail/Connect/Register") . "'>Create a new account</a> or <a href='" . $admin_helper->getUrl("alphamail/Connect/Login") . "'>sign in</a>. ";
                $html .= "For questions or help, visit <a href='http://amail.io/'>http://amail.io/</a> or contact our support at <a href='mailto:info@amail.io'>info@amail.io</a>.";
                $html .= "</div>";
            }
            else if(!$helper->isActivated())
            {
                $html = "<div class='notification-global'>";
                $html .= "The AlphaMail module is not activated. Please go to <a href='" . $admin_helper->getUrl('adminhtml/system_config/edit/section/alphamail') . "'>configuration</a> and activate it.";
                $html .= "</div>";
            }
            else if(($message = Mage::helper('alphamail/diagnostic')->getDiagnosticError()) != null)
            {
                $html = "<div class='notification-global'>";
                $html .= "AlphaMail: " . $message . " ";
                $html .= "<a href='" . $admin_helper->getUrl('adminhtml/system_config/edit/section/alphamail') . "'>Check the configuration</a>";
                $html .= "</div>";
            }
    		
            return $html;
        }
    }

?>