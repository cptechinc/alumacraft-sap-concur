<?php 
	/**
	 * Factory to instatiate Concur Endpoint classes, also provides them with the Access token needed
	 */
	Class ConcurFactory {
		use MagicMethodTraits;
		use ThrowErrorTrait;
		
		/**
		 * Is there an Error
		 * @var bool
		 */
		protected $error = false;
		
		/**
		 * Use to provide access to Servers
		 * @var string
		 */
		protected $accesstoken;
		
		/**
		 * Endpoints by their type => Class Name
		 * @var array
		 */
		protected $endpoints = array(
			'authentication' => 'Concur_Authentication',
			'vendor' => 'Concur_Vendor',
			'purchase-order' => 'Concur_PurchaseOrder',
			'purchase-order-receipts' => 'Concur_PurchaseOrderReceipts'
		);
		
		/**
		 * Creates factory then, we create an instance
		 * of the Authentication class which we use to retrieve
		 * an access token we can save to this class and supply it to the endpoint classes
		 */
		public function __construct() {
			$api = new Concur_Authentication();
			$api->create_authenticationtoken();
			
			if (!$api->response['error']) {
				$this->accesstoken = $api->response['access_token'];
			} else {
				echo json_encode($api->response);
				exit;
			}
		}
		
		public function create_endpoint($endpoint) {
			if (in_array($endpoint, array_keys($this->endpoints))) {
				$class = $this->endpoints[$endpoint];
				return new $class($this->accesstoken);
			} else {
				$this->error("Endpoint $endpoint does not exist");
			}
		}
	}
	
