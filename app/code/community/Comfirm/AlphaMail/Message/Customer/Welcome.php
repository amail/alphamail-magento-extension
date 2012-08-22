<?php

	class Comfirm_AlphaMail_Message_Customer_Welcome {
		public function __construct(){}

		public function load($default_data){
			$result = new stdClass();
			
			$helper = Mage::helper('alphamail/message');
			$customer = $default_data['customer'];
			
			$result->site = $helper->getSiteData();
			$result->customer = $helper->getCustomerData($customer);
			$result->recommendations = $helper->getRecommendationData(5);

			return $result;
		}
	}

?>