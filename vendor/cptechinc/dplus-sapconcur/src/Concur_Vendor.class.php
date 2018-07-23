<?php 
	class Concur_Vendor extends Concur_Endpoint {
		use ConstructAccessToken;
		
		protected $structurefile = 'vendor';
		protected $endpoints = array(
			'vendor' => 'https://www.concursolutions.com/api/v3.0/invoice/vendors'
		);
		
		public function get_vendors() {
			$url = new Purl\Url($this->endpoints['vendor']);
			$body = '';
			return $this->get_curl($url->getUrl(), $body);
		}
		
		public function does_vendorexist($vendorID) {
			$vendors = $this->get_vendor($vendorID);
			return ($vendors['TotalCount']) ? true : false;
		}
		
		public function get_vendor($vendorID) {
			$url = new Purl\Url($this->endpoints['vendor']);
			$url->query->set('vendorCode', $vendorID);
			return $this->get_curl($url->getUrl());
		}
		
		public function update_vendor($vendorID) {
			$dbvendor = get_dbvendor($vendorID);
			$vendor = $this->map_arraytostructure($dbvendor);
			$body = $this->get_vendorsendschema();
			$body['Items'][] = $vendor;
			$body['TotalCount'] = 1;
			$body['Vendor'][] = $vendor;
			return $this->put_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		public function get_vendorsendschema() {
			return array(
				'Items' => array(),
				'NextPage' => '',
				'RequestRunSummary' => '',
				'TotalCount' => 1,
				'Vendor' => array()
			);
		}
		
		public function add_vendor($vendorID) {
			$dbvendor = get_dbvendor($vendorID);
			$vendor = $this->map_arraytostructure($dbvendor);
			$body = $this->get_vendorsendschema();
			$body['Items'][] = $vendor;
			$body['TotalCount'] = 1;
			$body['Vendor'][] = $vendor;
			return $this->post_curl($this->endpoints['vendor'], $body, $json = true);
		}
		
		public function load_structure() {
			$file = $this->structuredirectory."$this->structurefile.json";
			
			if (file_exists($file)) {
				$this->structure = json_decode(file_get_contents($file), true);
				
				if (!$this->structure) {
					$this->error("failed to make structure out of $file");
				}
			} else {
				$this->error("$file does not exist to create json structure");
			}
		}
		
		public function map_arraytostructure($arraywithvalues) {
			$this->load_structure();
			$structuredarray = array();
			
			foreach ($this->structure as $key => $proparray) {
				if (empty($proparray['dbcolumn'])) {
					$structuredarray[$key] = $arraywithvalues[$key];
				} else {
					$structuredarray[$key] = $arraywithvalues[$proparray['dbcolumn']];
				}
			}
			return $structuredarray;
		}
	}
