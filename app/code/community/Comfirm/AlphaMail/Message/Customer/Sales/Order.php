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

			$currency_code = $order->getOrderCurrency()->getCode();
			$currency = Mage::app()->getLocale()->currency($currency_code);

			$result->items = $this->getOrderItemsData($order->getItemsCollection(), $currency);

			$result->billing_address = $this->_helper->getAddressData($order->getBillingAddress());
			$result->shipping_address = $this->_helper->getAddressData($order->getShippingAddress());

			$result->payment = $this->getOrderPaymentData($order, $currency);

			return $result;
		}

		private function getOrderActionUrl($order_id, $action){
			return Mage::getUrl('sales/order/'.$action.'/', array('order_id' => $order_id));
		}

		private function getOrderPaymentData($order, $currency){
			$result = new stdClass();

			$payment = $order->getPayment();
			$result->payment_id = (int)$payment->getId();
			$result->method = $payment->getMethod();
			$result->currency = $this->getOrderCurrencyData($order, $currency);
			$result->amount = $this->getOrderAmountData($order, $currency);

			return $result;
		}

		private function getOrderItemsData($order_items, $currency){
			$items = array();
			
			foreach($order_items as $item){
				// Skip child items
    			if($item->getParentItem()) continue;

				$result = new stdClass();

				$result->product_id = (int)$item->getId();
		        $result->name = $item->getName();
		        $result->sku = $item->getSku();
		        $result->quantity = (int)$item->getQtyOrdered();

		        $product = $this->_helper->getOrderItemProduct($item);

		        $result->url = $product->getProductUrl();
		        $result->images = $this->_helper->getProductItemImages($product);

		        $result->price = (float)$item->getPrice();
		        $result->price_formatted = $currency->toCurrency($result->price);

	        	$items[] = $result;
			}

			return $items;
		}

		private function getOrderAmountData($order, $currency){
			$result = new stdClass();

	        $result->tax = (float)$order->getTaxAmount();
	        $result->shipping = (float)$order->getShippingAmount();
	        $result->discount = (float)$order->getDiscountAmount();
	        $result->sub_total = (float)$order->getSubtotal();
	        $result->grand_total = (float)$order->getGrandTotal();

	        $result->tax_formatted = $currency->toCurrency($result->tax);
	        $result->shipping_formatted = $currency->toCurrency($result->shipping);
	        $result->discount_formatted = $currency->toCurrency($result->discount);
	        $result->sub_total_formatted = $currency->toCurrency($result->sub_total);
	        $result->grand_total_formatted = $currency->toCurrency($result->grand_total);

			return $result;
		}

		private function getOrderCurrencyData($order, $currency){
			$result = new stdClass();

			$result->code = $currency_code = $order->getOrderCurrency()->getCode();
			$result->symbol = $currency->getSymbol();

			return $result;
		}
	}

?>