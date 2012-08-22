<?php

	class Comfirm_AlphaMail_Message_Customer_Sales_Order {
		protected $_helper = null;

		public function __construct(){
			$this->_helper = Mage::helper('alphamail/message');
		}

		public function load($default_data){
			$result = new stdClass();

			$order = $default_data['order']; // Mage_Sales_Model_Order
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());

			$result->site = $this->_helper->getSiteData();
			$result->customer = $this->_helper->getCustomerData($customer);
			$result->order = $this->getOrderData($order);

			$result->recommendations = $this->_helper->getRecommendationData(5, $order);

			return $result;
		}

		private function getOrderData($order){
			$result = new stdClass();

			$result->order_id = $order_id = (int)$order->getId();
			$result->order_real_id = $order->getRealOrderId();

			$result->url = $this->getOrderActionUrl($order_id, "view");
			$result->reorder_url = $this->getOrderActionUrl($order_id, "reorder");
			$result->print_url = $this->getOrderActionUrl($order_id, "print");

			$result->items = $this->getOrderItemsData($order->getItemsCollection());

			$result->billing_address = $this->_helper->getAddressData($order->getBillingAddress());
			$result->shipping_address = $this->_helper->getAddressData($order->getShippingAddress());

			$result->payment = $this->getOrderPaymentData($order);

			return $result;
		}

		private function getOrderActionUrl($order_id, $action){
			return Mage::getUrl('sales/order/'.$action.'/', array('order_id' => $order_id));
		}

		private function getOrderPaymentData($order){
			$result = new stdClass();

			$payment = $order->getPayment();
			$result->payment_id = (int)$payment->getId();
			$result->method = $payment->getMethod();
			$result->currency = $this->getOrderCurrencyData($order);
			$result->amount = $this->getOrderAmountData($order);

			return $result;
		}

		private function getOrderItemsData($order_items){
			$items = array();
			
			foreach($order_items as $item){
				$result = new stdClass();
				$product = $item->getProduct();
				
				$result->product_id = (int)$item->getId();
		        $result->name = $item->getName();
		        $result->sku = $item->getSku();
		        $result->quantity = (int)$item->getQtyOrdered();
		        $result->url = $product->getProductUrl();
		        $result->images = $this->_helper->getProductItemImages($product);

		        $result->price = (float)$item->getPrice();

	        	$items[] = $result;
			}

			return $items;
		}

		private function getOrderAmountData($order){
			$result = new stdClass();

	        $result->tax = (float)$order->getTaxAmount();
	        $result->shipping = (float)$order->getShippingAmount();
	        $result->discount = (float)$order->getDiscountAmount();
	        $result->sub_total = (float)$order->getSubtotal();
	        $result->grand_total = (float)$order->getGrandTotal();

			return $result;
		}

		private function getOrderCurrencyData($order){
			$result = new stdClass();

			$result->code = $currency_code = $order->getOrderCurrency()->getCode();
			$result->symbol = Mage::app()->getLocale()->currency($currency_code)->getSymbol();

			return $result;
		}
	}

?>