<?php 
	/**
	 * Template Class to build endpoint classes from and extend
	 */
	abstract class Concur_Endpoint {
		use MagicMethodTraits;
		use ThrowErrorTrait;
		
		/**
		 * List of URL Endpoints
		 * @var array
		 */
		protected $endpoints;
		
		/**
		 * Response from cURL or Endpoint
		 * @var array
		 */
		protected $response;
		
		/**
		 * Sends POST cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send
		 * @param  bool   $json Send body as JSON?
		 * @return array        Response from Endpoint or response from cURL
		 */
		protected function post_curl($url, $body, $json = false) {
			$headers = $this->generate_defaultcurlheader();
			
			if ($json) {
				$headers[] = 'Content-type: application/json';
				$body = json_encode($body);
			}
			
			$curl = $this->get_defaultcurl($url, $headers);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			return $this->execute_andgetresponse($curl);
		}
		
		/**
		 * Sends PUT cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send
		 * @param  bool   $json Send body as JSON?
		 * @return array        Response from Endpoint or response from cURL
		 */
		protected function put_curl($url, $body, $json = false) {
			$headers = $this->generate_defaultcurlheader();
			
			if ($json) {
				$headers[] = 'Content-type: application/json';
				$body = json_encode($body);
			}
			
			$curl = $this->get_defaultcurl($url, $headers);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
			return $this->execute_andgetresponse($curl);
		}
		
		/**
		 * Sends GET cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send //NOTE NOT USED
		 * @return array        Response from Endpoint or response from cURL
		 */
		protected function get_curl($url, $body = '') {
			$headers = $this->generate_defaultcurlheader();
			$curl = $this->get_defaultcurl($url, $headers);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
			return $this->execute_andgetresponse($curl);
		}
		
		/**
		 * Creates the default HTTP HEADERS Array used for cURL
		 * // NOTE IT REAUTHENTICATES IF TOKEN HAS EXPIRED
		 * @return array default HTTP HEADER
		 */
		protected function generate_defaultcurlheader() {
			if (strtotime('now') > Concur_Authentication::$tokenexpires) {
				Concur_Authentication::re_authenticate();
			}
			$accesstoken = Concur_Authentication::$authtoken;
			return $headers = [
				'Connection: close',
				'Accept: application/json',
				"Authorization: Bearer $accesstoken"
			];
		}
		
		/**
		 * Initializes and sets the default options for a cURL Resource
		 * @param  string $url     URL to send cURL request
		 * @param  array $headers  Array of Header Options
		 * @return resource        Default cURL Resource
		 */
		protected function get_defaultcurl($url, $headers) {
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_RETURNTRANSFER => 1,
				CURLOPT_URL => $url,
				CURLOPT_HTTPHEADER => $headers
			));
			return $curl;
		}
		
		/**
		 * Executes cURL Requests and Handles the response
		 * to set the $this->response property and to return it
		 * @param  resource $curl cURL resource
		 * @return array          Response from request execution
		 */
		protected function execute_andgetresponse($curl) {
			$this->response = json_decode(curl_exec($curl), true);
			
			if ($this->response) {
				if (!isset($this->response['error'])) {
					$this->response['error'] = false;
				}
			} else {
				$this->response = array(
					'error' => true,
					'message' => curl_error($curl),
					'response' => false
				);
			}
			return $this->response;
		}
		
		/**
		 * Writes to Error Log
		 * @param  string $error Error Message
		 * @return void
		 */
		protected function log_error($error) {
			$date = date("Y-m-d h:m:s");
			$class = get_class($this);
			$message = "[{$date}] [{$class}] $error";
			DplusWire::wire('log')->save('sap-errors', $message);
		}
		
		/**
		 * Cleanses the value using str_replace
		 * @param  string $value string
		 * @return string        Sanitized String
		 */
		protected function clean_value($value) {
			$replace = array(
				"\r" => '',
				"\n" => ''
			);
			return trim(str_replace(array_keys($replace), array_values($replace), $value));
		}
	}
