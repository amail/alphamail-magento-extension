<?php

	class Comfirm_AlphaMail_Message_Customer_Sales_Order_Invoice extends Comfirm_AlphaMail_Message_Customer_Sales_Order {
		public function __construct(){
			parent::__construct();
		}	

		public function load($default_data){
			$result = parent::load($default_data);

			$result->admin = $this->_helper->getAdminUserData();

			$invoice = $default_data['invoice'];
			$result->invoice = $this->getInvoiceData($invoice);

			return $result;
		}

		private function getInvoiceData($invoice){
			$result = new stdClass();

			$result->invoice_id = (int)$invoice->getId();
			$result->is_last = $invoice->isLast();
			$result->state = $this->getInvoiceState($invoice);
			$result->options = $this->getInvoiceOptionsData($invoice);

			return $result;
		}

		private function getInvoiceOptionsData($invoice){
			$result = new stdClass();

			$result->can_refund = (bool)$invoice->canRefund();
			$result->can_void = (bool)$invoice->canVoid();
			$result->can_capture = (bool)$invoice->canCapture();
			$result->can_cancel = (bool)$invoice->canCancel();

			return $result;
		}

		private function getInvoiceState($invoice){
			$result = new stdClass();

			$result->state_id = (int)$invoice->getState();
			$result->name = $invoice->getStateName();

			return $result;
		}
	}

?>