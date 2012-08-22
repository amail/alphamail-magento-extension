<?php

	class Comfirm_AlphaMail_Helper_Message extends Mage_Core_Helper_Abstract {
	    public function getSiteData(){
	        $result = new stdClass();
	        
	        $result->name = Mage::app()->getStore()->getFrontendName();
	        $result->base_url = Mage::app()->getStore()->getBaseUrl();
	        $result->locale_code = Mage::app()->getLocale()->getLocaleCode(); // E.g. sv_SE

	        return $result;
	    }

	    public function getAdminUserData(){
			$result = new stdClass();

			$admin_user = Mage::getSingleton('admin/session')->getUser();

			if($admin_user != null){
				$result->user_id = $admin_user->getUserId();
				$result->user_name = $admin_user->getUsername();
				
				$result->name = $admin_user->getName();
				$result->first_name = $admin_user->getFirstname();
				$result->last_name = $admin_user->getLastname();
			}

			return $result;
		}

	    public function getCustomerData($customer){
	        $result = null;

	        if($customer != null && $customer->getId() != null){
	            $result = new stdClass();

	            $result->customer_id = (int)$customer->getId();
	            $result->email = $customer->getEmail();

	            $result->name = $customer->getName();
	            $result->first_name = $customer->getFirstname();
	            $result->last_name = $customer->getLastname();

	            $result->group = $this->getCustomerGroupData($customer);

	            if($customer->getPrimaryAddress() != null){
	                $result->address = $this->getAddressData($customer->getPrimaryAddress());
	            }
	        }

	        return $result;
	    }

	    private function getCustomerGroupData($customer){
	    	$result = new stdClass();

	    	$result->group_id = $group_id = (int)$customer->getGroupId();
	    	$group = Mage::getModel ('customer/group')->load ($group_id);
			$result->code = $group->getCode();

	    	return $result;
	    }

	    public function getAddressData($address){
	        $result = new stdClass();

	        $result->name = $address->getName();
	        $result->street = (array)preg_split('@\n@', $address->getStreetFull(), NULL, PREG_SPLIT_NO_EMPTY);

	        $result->region = $this->getAddressRegionData($address);

	        $result->phonenumber = $address->getTelephone();
	        $result->faxnumber = $address->getFax();

	        $country_model = $address->getCountryModel();
	        if($country_model != null){
	            $result->country = $country_model->getName();
	        }

	        return $result;
	    }

	    public function getAddressRegionData($address){
	        $result = new stdClass();

	        $result->name = $address->getRegion();
	        $result->postal_code = $address->getPostcode();
	        $result->city = $address->getCity();

	        // Only add a region code if it differs from the region name.
	        if($address->getRegion() != $address->getRegionCode()){
	            $result->code = $address->getRegionCode();
	        }

	        return $result;
	    }

	    public function getRecommendationData($limit_per_list, $order = null){
	        $result = new stdClass();

	        $result->topsellers = $this->getTopSellingProducts($limit_per_list);

	        if($order != null){
	        	$related_items = array();
	        	$upsell_items = array();
	        	$cross_sell_items = array();

	        	// Make a lookup table with IDs for all products in the order
	        	$products_lookup = array();
	        	foreach($order->getItemsCollection() as $item){
        			$products_lookup[(int)$item->getId()] = false;
	        	}

	        	// Scan for related products
	        	foreach($order->getItemsCollection() as $item){
	        		$product = $item->getProduct();

	        		// Need to clean this up, break out to sepearet function.
	        		// Also optimize. E.g. products can be cached, in order to save data recommendations
	        		// could have a products array and top selling, related, upsell, cross-sell could just be product IDs.
	        		// Could also be a clean way to have an array of products to recommend if you just want to print some
	        		// products without having to iterate all arrays.

	        		// Get related products
	        		foreach($product->getRelatedProductCollection() as $related_product){
	        			// Only include products that aren't in the order
	        			if(!array_key_exists((int)$related_product->getId(), $products_lookup)){
		        			if(count($related_items) < $limit_per_list){
					            $related_product->load();
		        				$related_items[] = $this->getProductData($related_product);
		        			}else{
		        				break;
		        			}
		        		}
	        		}

	        		// Get upsell products
	        		foreach($product->getUpSellProductCollection() as $upsell_product){
	        			// Only include products that aren't in the order
	        			if(!array_key_exists((int)$upsell_product->getId(), $products_lookup)){
		        			if(count($upsell_items) < $limit_per_list){
					            $upsell_product->load();
		        				$upsell_items[] = $this->getProductData($upsell_product);
		        			}else{
		        				break;
		        			}
		        		}
	        		}

	        		// Get cross sell products
	        		foreach($product->getCrossSellProductCollection() as $cross_sell_product){
	        			// Only include products that aren't in the order
	        			if(!array_key_exists((int)$cross_sell_product->getId(), $products_lookup)){
		        			if(count($cross_sell_items) < $limit_per_list){
					            $cross_sell_product->load();
		        				$cross_sell_items[] = $this->getProductData($cross_sell_product);
		        			}else{
		        				break;
		        			}
		        		}
	        		}
	        	}

	        	$result->related = $related_items;
	        	$result->upsell = $upsell_items;
	        	$result->cross_sell = $cross_sell_items;
	        }

	        return $result;
	    }

	    /*public function getUniqueProductsFromCollection($product_lookup, $product_collection, $item_limit){
	    	$products = array();

        	// Scan for related products
        	foreach($product_collection as $product_item){
        		$product = $product_item->getProduct();
        		foreach($product->getRelatedProductCollection() as $related_product){
        			// Only include products that aren't in the order
        			if(!array_key_exist((int)$related_product->getId(), $products_lookup)){
	        			if(count($products) < $item_limit){
				            $related_product->load();
	        				$products[] = $this->getProductData($related_product);
	        			}else{
	        				break 2;
	        			}
	        		}
        		}
        	}

        	return $products;
	    }*/

	    public function getProductItemImages($product_item){
	        $result = new stdClass();

	        $result->small = $product_item->getSmallImageUrl();
	        $result->thumbnail = $product_item->getThumbnailUrl();

	        return $result;
	    }

	    private function getTopSellingProducts($limit){
	        $items = array();

	        $store_id = Mage::app()->getStore()->getId();

	        $products = Mage::getResourceModel('reports/product_collection')
	            ->addOrderedQty()
	            ->addAttributeToSelect('*')
	            ->setStoreId($store_id)
	            ->addStoreFilter($store_id)
	            ->addViewsCount();

	        $products->setPageSize($limit)->setCurPage(1);

	        foreach ($products as $product) {
	            $item = new stdClass();

	            $product->load();
	            $item = $this->getProductData($product);

	            /*$item->product_id = (int)$product->getId();
	            $item->name = $product->getName();
	            $item->sku = $product->getSku();
	            $item->url = $product->getProductUrl();
	            $item->images = $this->getProductItemImages($product);
	            $item->price = (float)$product->getPrice();*/

	            $items[] = $item;
	        }

	        return $items;
	    }

	    private function getProductData($product){
	            $item = new stdClass();
	            
	            $item->product_id = (int)$product->getId();
	            $item->name = $product->getName();
	            $item->sku = $product->getSku();
	            $item->url = $product->getProductUrl();
	            $item->images = $this->getProductItemImages($product);
	            $item->price = (float)$product->getPrice();

	            $base_currency_code = Mage::app()->getStore()->getBaseCurrencyCode();
	            $current_currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();

	            // If currencies differ, then convert to the correct currency
	            if($base_currency_code != $current_currency_code && $item->price > 0){
	                $item->price = Mage::helper('directory')->currencyConvert($item->price,
	                    $base_currency_code, $current_currency_code);
	            }

	            return $item;
	    }
	}

?>