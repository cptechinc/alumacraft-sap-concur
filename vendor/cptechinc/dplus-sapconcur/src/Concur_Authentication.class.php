<?php 
	/**
	 * Class for getting Authentication through Concur's API
	 */
	class Concur_Authentication extends Concur_Endpoint {
		protected $endpoints = array(
			'authentication' => 'https://us.api.concursolutions.com/oauth2/v0/token'
		);
		
		/**
		 * Concur Access Token
		 * @var string
		 */
		static $authtoken;
		
		/**
		 * Time Stamp of when access token Expires
		 * @var int
		 */
		static $tokenexpires;
		
		/**
		 * Sends POST cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send
		 * @param  bool   $json Send body as JSON?
		 * @return array        Response from Endpoint or response from cURL
		 */
		protected function post_curl($url, $body, $json = false) {
			$headers = $this->generate_defaultcurlheader();
			$curl = $this->get_defaultcurl($url, $headers);
			curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($body));
			return $this->execute_andgetresponse($curl);
		}
		
		/**
		 * Creates the default HTTP HEADERS Array used for cURL
		 * @return array default HTTP HEADER
		 */
		protected function generate_defaultcurlheader() {
			return $headers = [
				'Connection: close',
				'Accept: application/json'
			];
		}
		
		/**
		 * Gets and Sets the authentication token for accessing the Concur API
		 * @return array JSON Response
		 */
		public function create_authenticationtoken() {
			$appconfig = DplusWire::wire('pages')->get('/config/');
			$body = [
				'client_id' => $appconfig->client_id,
				'client_secret' => $appconfig->client_secret,
				'grant_type' => 'password',
				'username' => $appconfig->concur_username,
				'password' => $appconfig->concur_password,
				'credtype '=>'password',
			];
			$this->post_curl($this->endpoints['authentication'], $body);
			$this->accesstoken = $this->response['error'] ? false : $this->response['access_token'];
			self::$authtoken = $this->response['error'] ? false : $this->response['access_token'];
			self::$tokenexpires = $this->response['error'] ? false : strtotime('now') + 3600;
			return $this->response;
		}
	}
