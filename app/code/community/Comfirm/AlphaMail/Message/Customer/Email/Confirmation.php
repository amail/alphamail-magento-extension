<?php

	class Comfirm_AlphaMail_Message_Customer_Email_Confirmation {
		public function __construct(){}

		public function load($default_data){
			$result = new stdClass();
			
			$helper = Mage::helper('alphamail/message');
			$customer = $default_data['customer'];

			$result->site = $helper->getSiteData();
			$result->customer = $helper->getCustomerData($customer);

			$result->confirmation = new stdClass();
			$result->confirmation->key = $customer->getConfirmation();
			$result->confirmation->url = Mage::getUrl(
				'customer/account/confirm/', array(
					'_query' => array(
						'id' => $customer->getId(),
						'key' => $customer->getConfirmation(),
						'back_url' => Mage::app()->getStore()->getBaseUrl()
					)
				)
			);

			return $result;
		}
	}

?>