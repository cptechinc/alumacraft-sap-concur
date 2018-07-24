<?php 
	/**
	 * Class to handle dealing with Vendors 
	 */
	class Concur_Vendor extends Concur_Endpoint implements StructuredClass {
		use ConstructAccessToken;
		use StructuredClass;
		
		protected $structurefile = 'vendor';
		protected $endpoints = array(
			'vendor' => 'https://www.concursolutions.com/api/v3.0/invoice/vendors'
		);
		
		/**
		 * Sends GET request for vendors and returns the response
		 * @return array Response
		 */
		public function get_vendors() {
			$url = new Purl\Url($this->endpoints['vendor']);
			$body = '';
			return $this->get_curl($url->getUrl(), $body);
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
		 * Sends a PUT request to update current Vendor at Concur
		 * @param  string $vendorID Vendor ID to use to load from database
		 * @return array            Response
		 */
		public function update_vendor($vendorID) {
			$dbvendor = get_dbvendor($vendorID);
			$vendor = $this->map_arraytostructure($dbvendor);
			$body = $this->get_vendorsendschema();
			$body['Items'][] = $vendor;
			$body['TotalCount'] = 1;
			$body['Vendor'][] = $vendor;
			return $this->put_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		/**
		 * Sends a PUT request to add Vendor at Concur
		 * @param  string $vendorID Vendor ID to use to load from database
		 * @return array            Response
		 */
		public function add_vendor($vendorID) {
			$dbvendor = get_dbvendor($vendorID);
			$vendor = $this->map_arraytostructure($dbvendor);
			$body = $this->get_vendorsendschema();
			$body['Items'][] = $vendor;
			$body['TotalCount'] = 1;
			$body['Vendor'][] = $vendor;
			return $this->post_curl($this->endpoints['vendor'], $body, $json = true);
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
