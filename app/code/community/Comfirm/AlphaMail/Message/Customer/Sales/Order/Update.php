<?php

	class Comfirm_AlphaMail_Message_Customer_Sales_Order_Update extends Comfirm_AlphaMail_Message_Customer_Sales_Order {
		public function __construct(){
			parent::__construct();
		}	

		public function load($default_data){
			$result = parent::load($default_data);

			$result->admin = $this->_helper->getAdminUserData();

			$result->comment = new stdClass();
			$result->comment->text = $default_data['comment'];

			return $result;
		}
	}

?>