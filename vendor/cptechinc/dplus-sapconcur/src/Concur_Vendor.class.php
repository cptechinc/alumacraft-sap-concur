<?php 
	/**
	 * Class to handle dealing with Vendors 
	 */
	class Concur_Vendor extends Concur_Endpoint {
		use ConstructAccessToken;
		use StructuredClassTraits;
		
		protected $endpoints = array(
			'vendor' => 'https://www.concursolutions.com/api/v3.0/invoice/vendors'
		);
		
		/**
		 * Structure for Vendor Array
		 * @var array
		 */
		protected $structure = array(
			'header' => array(
				'VendorCode' 						=> array('dbcolumn' => '', 'required' => false),
				'VendorName' 						=> array('dbcolumn' => '', 'required' => false),
				'AddressCode' 						=> array('dbcolumn' => '', 'required' => false),
				'Address1' 							=> array('dbcolumn' => '', 'required' => false),
				'Address2' 							=> array('dbcolumn' => '', 'required' => false),
				'Address3' 							=> array('dbcolumn' => '', 'required' => false),
				'City' 								=> array('dbcolumn' => '', 'required' => false),
				'State' 							=> array('dbcolumn' => '', 'required' => false),
				'PostalCode' 						=> array('dbcolumn' => '', 'required' => false),
				'CountryCode' 						=> array('dbcolumn' => '', 'required' => false),
				'Country' 							=> array('dbcolumn' => 'CountryCode', 'required' => false),
				'Approved' 							=> array('dbcolumn' => '', 'required' => false),
				'PaymentTerms' 						=> array('dbcolumn' => '', 'required' => false),
				'AccountNumber' 					=> array('dbcolumn' => '', 'required' => false),
				'TaxID' 							=> array('dbcolumn' => '', 'required' => false),
				'ProvincialTaxID' 					=> array('dbcolumn' => '', 'required' => false),
				'TaxType' 							=> array('dbcolumn' => '', 'required' => false),
				'CurrencyCode' 						=> array('dbcolumn' => '', 'required' => false),
				'ShippingMethod' 					=> array('dbcolumn' => '', 'required' => false),
				'ShippingTerms' 					=> array('dbcolumn' => '', 'required' => false),
				'DiscountTermsDays' 				=> array('dbcolumn' => '', 'required' => false),
				'DiscountPercentage' 				=> array('dbcolumn' => '', 'required' => false),
				'ContactFirstName' 					=> array('dbcolumn' => '', 'required' => false),
				'ContactLastName' 					=> array('dbcolumn' => '', 'required' => false),
				'ContactPhoneNumber' 				=> array('dbcolumn' => '', 'required' => false),
				'ContactEmail' 						=> array('dbcolumn' => '', 'required' => false),
				'PurchaseOrderContactFirstName' 	=> array('dbcolumn' => '', 'required' => false),
				'PurchaseOrderContactLastName' 		=> array('dbcolumn' => '', 'required' => false),
				'PurchaseOrderContactPhoneNumber' 	=> array('dbcolumn' => '', 'required' => false),
				'PurchaseOrderContactEmail' 		=> array('dbcolumn' => '', 'required' => false),
				'DefaultEmployeeID' 				=> array('dbcolumn' => '', 'required' => false),
				'DefaultExpenseTypeName' 			=> array('dbcolumn' => '', 'required' => false),
				'PaymentMethodType' 				=> array('dbcolumn' => '', 'required' => false),
				'AddressImportSyncID' 				=> array('dbcolumn' => '', 'required' => false)
			)
		);
		
		/**
		 * Sends GET request for vendors and returns the response
		 * @param  string $url URL to fetch Vendors
		 * @return array       Response
		 */
		public function get_vendors($url = '') {
			$url = !empty($url) ? new Purl\Url($url) : new Purl\Url($this->endpoints['vendor']);
			$url->query->set('limit', '1000');
			$body = '';
			return $this->get_curl($url->getUrl(), $body);
		}
		
		/**
		 * Returns an array of Existing Vendor Codes
		 * @return array Vendor Codes
		 */
		public function get_existingvendors() {
			$response = $this->get_vendors();
			$vendorlist = array_column($response['Vendor'], 'VendorCode');
			
			while (!empty($response['NextPage'])) {
				$response = $this->get_vendors($response['NextPage']);
				$vendorlist = array_merge($vendorlist,array_column($response['Vendor'], 'VendorCode'));
			}
			return $vendorlist;
		}
		
		/**
		 * Sends a GET request for a vendor and returns the response
		 * @param  string $vendorID Vendor ID to grab Vendor
		 * @return array            Response
		 */
		public function get_vendor($vendorID) {
			$url = new Purl\Url($this->endpoints['vendor']);
			$url->query->set('vendorCode', $vendorID);
			return $this->get_curl($url->getUrl());
		}
		
		/**
		 * Sends request for Getting Vendor and sees if there's one vendor that matches 
		 * with the vendorCode field
		 * @param  string $vendorID Vendor ID to validate
		 * @return bool             Does Vendor Exist at Concur
		 */
		public function does_vendorexist($vendorID) {
			$vendors = $this->get_vendor($vendorID);
			return ($vendors['TotalCount']) ? true : false;
		}
		
		/**
		 * Sends a POST request to create Vendor at Concur
		 * @param  string $vendorID Vendor ID to use to load from database
		 * @return array            Response
		 */
		public function create_vendor($vendorID) {
			$dbvendor = get_dbvendor($vendorID);
			$vendor = $this->create_sectionarray($this->structure['header'], $dbvendor);
			$body = $this->get_vendorsendschema();
			$body['Items'][] = $vendor;
			$body['TotalCount'] = 1;
			$body['Vendor'][] = $vendor;
			return $this->post_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		/**
		 * Sends a POST request to create Vendors at Concur
		 * @param  array $vendors   Vendor IDs to use to load from database
		 * @return array            Response
		 */
		public function create_vendors($vendors) {
			$body = $this->get_vendorsendschema();
			$vendorIDs = get_dbvendorsinclude($vendors);
			
			foreach ($vendorIDs as $dbvendor) {
				$vendor = $this->create_sectionarray($this->structure['header'], $dbvendor);
				$body['Items'][] = $vendor;
				$body['Vendor'][] = $vendor;
			}
			$body['TotalCount'] = sizeof($vendors);
			return $body;
			return $this->post_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		/**
		 * Sends a PUT request to update current Vendor at Concur
		 * @param  string $vendorID Vendor ID to use to load from database
		 * @return array            Response
		 */
		public function update_vendor($vendorID) {
			$dbvendor = get_dbvendor($vendorID);
			$vendor = $this->create_sectionarray($this->structure['header'], $dbvendor);
			$body = $this->get_vendorsendschema();
			$body['Items'][] = $vendor;
			$body['TotalCount'] = 1;
			$body['Vendor'][] = $vendor;
			return $this->put_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		/**
		 * Sends a PUT request to update current Vendors at Concur
		 * @param  array $vendors  Vendor IDs
		 * @return array            Response
		 */
		public function update_vendors(array $vendors) {
			$body = $this->get_vendorsendschema();
			$vendorIDs = get_dbvendorsinclude($vendors);
			
			foreach ($vendorIDs as $dbvendor) {
				$vendor = $this->create_sectionarray($this->structure['header'], $dbvendor);
				$body['Items'][] = $vendor;
				$body['TotalCount'] = sizeof($vendors);
				$body['Vendor'][] = $vendor;
			}
			return $body;
			return $this->put_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		/**
		 * Verifies if Vendor Exists at Concur
		 * If it exists then it updates the Vendor
		 * If not, it will create the Vendor
		 * @param  string $vendorID  Vendor Code
		 * @return void
		 */
		public function send_vendor($vendorID) {
			if ($this->does_vendorexist($vendorID)) {
				$this->update_vendor($vendorID);
			} else {
				$this->create_vendor($vendorID);
			}
		}
		
		public function batch_vendors() {
			$existingvendors = $this->get_existingvendors();
			$newvendors = get_dbvendors($existingvendors);
			$response = array();
			$response['updated'] = $this->update_vendors($existingvendors);
			$response['created'] = $this->create_vendors($newvendors);
			$this->response = $response;
			return $this->response;
		}
		
		/**
		 * Returns the array format to send updates or adding of vendors at Concur API
		 * @return array Update Array
		 */
		public function get_vendorsendschema() {
			return array(
				'Items' => array(),
				'NextPage' => '',
				'RequestRunSummary' => '',
				'TotalCount' => 1,
				'Vendor' => array()
			);
		}
	}
