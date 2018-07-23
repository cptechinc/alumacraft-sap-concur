<?php
	/**
	 * Trait for the SAP Concur Endpoint classes so they have constructors that provides the Access Token
	 */
	trait ConstructAccessToken {
		/**
		 * Constructor
		 * @param string $accesstoken Concur API Token
		 */
		public function __construct($accesstoken) {
			$this->accesstoken = $accesstoken; 
		}
	}
