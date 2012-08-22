<?php

	class Comfirm_AlphaMail_Message_Customer_Password_Renewal {
		public function __construct(){}

		public function load($default_data){
			$result = new stdClass();

			$helper = Mage::helper('alphamail/message');
			$customer = $default_data['customer'];

			$result->site = $helper->getSiteData();
			$result->customer = $helper->getCustomerData($customer);

			$result->reset_password = new stdClass();
			$result->reset_password->token = $customer->getRpToken();
			$result->reset_password->url = Mage::getUrl(
				'customer/account/resetpassword/', array(
					'_query' => array(
						'id' => $customer->getId(),
						'token' => $customer->getRpToken()
					)
				)
			);

			$result->recommendations = $helper->getRecommendationData(5);

			return $result;
		}
	}

?>