<?php 
    namespace Dplus\Base;
    
    /**
     * Class for sending cURL Requests to Servers
     */
    class Curl {
        use MagicMethodTraits;
		use ThrowErrorTrait;
        
        /**
         * Types to Accept
         * // NOTE also can be used for Content Types
         * @var array
         */
        protected $accept_types = array(
            'json'             => 'application/json',
            'application/json' => 'application/json',
            'csv'              => 'text/csv',
            'text/csv'         => 'text/csv',
            'zip'              => 'application/zip',
            'application/zip'  => 'application/zip',
            'url'              => 'application/x-www-form-urlencoded',
        );
        
        /**
         * Types to Accept for this request
         * @var array
         */
        protected $accepting = array();
        
        /**
         * Authentication
         * @var HTTPAuthentication
         */
        protected $authentication;
        
        /**
         * Content Type to send
         * @var string
         */
        protected $contenttype;
        
        /**
         * Response from Server
         * @var mixed
         */
        protected $response;
        
        /**
         * Content Type of Response
         * @var string
         */
        protected $responseformat;
        
        /**
         * Adds Accept Type to the $this->accepting array
         * @param string $accept Accept type
         */
        public function add_acceptheader($accept) {
            if (in_array($accept, array_keys($this->accept_types))) {
                if (!in_array($this->accept_types[$accept], $this->accepting)) {
                    $this->accepting[] = $this->accept_types[$accept];
                }
            } else {
                $this->error("$accept is not a valid Accept Header");
            }
        }
        
        /**
         * Sets the Content Type that will be sent and defined to the server
         * @param string $type Content Type
         */
        public function set_contenttype($type = '') {
            if (empty($type)) return true;
            
            if (in_array($type, array_keys($this->accept_types))) {
                $this->contenttype = $this->accept_types[$type];
            } else {
                $this->error("$type is not a valid Content Type");
            }
        }
        
        /**
         * Sets the $this->authentication type by making a class of that HTTPAuthentication_{$type}
         * @param string $type Authentication Type e.g. Oauth2
         */
        public function set_authentication($type) {
            if (in_array($type, HTTPAuthentication::$types)) {
                $this->authentication = HTTPAuthentication::create($type);
            } else {
                $this->error("$type is not a valid authentication type");
            }
        }
        
        /**
         * Builds the Headers Array for a cURL Request
         * @return array 
         */
        protected function build_headers() {
            $headers = [
                'Connection: close',
                'Cache-Control: no-cache'
            ];
            $headers[] = "Accept: " . implode(', ', $this->accepting);
            
            if (!empty($this->authentication)) {
                $headers[] = $this->authentication->build_authenticationheader();
            }
            if (!empty($this->contenttype)) {
                $headers[] = "Content-type: $this->contenttype";
            }
            return $headers;
        }
        
        /**
         * Creates default Curl
         * @param  string   $url cURL request Destination
         * @return resource      Default cURL Resource
         */
        public function create_curl($url) {
            $headers = $this->build_headers();
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
		protected function execute($curl) {
            $this->response = array(
                'server' => array(
                    'error' => false,
                    'message' => '',
                    'http_code' => ''
                ),
                'response' => false
            );
            
			$this->response['response'] = $this->is_jsonrequest() ? json_decode(curl_exec($curl), true) : curl_exec($curl);
			$curlinfo = curl_getinfo($curl);
			$this->response['server']['http_code'] = $curlinfo['http_code'];
			
			if ($this->response) {
                if ($this->is_jsonrequest()) {
                    if (!isset($this->response['response']['error'])) {
    					$this->response['response']['error'] = false;
    				}
                }
			} else {
                $this->response = array(
                    'server' => array(
                        'error' => true,
                        'message' => curl_error($curl),
                        'http_code' => 404
                    ),
                    'response' => false
                );
			}
			return $this->response;
		}
        
        /**
		 * Sends GET cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send //NOTE NOT USED
		 * @return array        Response from Endpoint or response from cURL
		 */
        public function get($url, $body = '') {
            $curl = $this->create_curl($url);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
            return $this->execute($curl);
        }
        
        /**
		 * Sends POST cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send
		 * @return array        Response from Endpoint or response from cURL
		 */
        public function post($url, $body) {
            $body = $this->format_body($body);
            $curl = $this->create_curl($url);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
            return $this->execute($curl);
        }
        
        /**
		 * Sends PUT cURL request
		 * @param  string $url  URL to make request to
		 * @param  array  $body Key Value array to send
		 * @return array        Response from Endpoint or response from cURL
		 */
        public function put($url, $body) {
            $body = $this->format_body($body);
            $curl = $this->create_curl($url);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            return $this->execute($curl);
        }
        
        /**
         * Returns if the Content Type is JSON
         * @return bool is Content Type JSON?
         */
        public function is_jsonrequest() {
            return in_array($this->accept_types['json'], $this->accepting);
        }
        
        /**
         * Takes Body and Parses it into format needed
         * @param  mixed $body  Content To Send
         * @return mixed        Content After Parsing
         */
        public function format_body($body) {
            $format = array_search($this->contenttype, $this->accept_types);
            switch ($format) {
                case 'url':
                    return http_build_query($body);
                    break;
                case 'text':
                    return $body;
                    break;
                case 'json':
                    return json_encode($body);
                    break;
            }
        }
    }
    
    /**
     * Class to handle Creation of Authentication Classes
     */
    class HTTPAuthentication {
        use MagicMethodTraits;
		use ThrowErrorTrait;
        
        /**
         * Authentication Types
         * @var array
         */
        static $types = array('oauth2');
        
        /**
         * Authentication Type
         * @var string
         */
        protected $type;
        
        /**
         * Returns Class for Authentication Type
         * @param                      string $type Authentication Type
         * @return HTTPAuthentication         Authentication
         */
        static function create($type) {
            $type = strtolower($type);
            
            if (in_array($type, self::$types)) {
                $class = "HTTPAuthentication_".ucfirst($type);
                return new $class();
            } else {
                $httpauth = new HTTPAuthentication();
                $httpauth->error("$type is not a valid HTTP Authentication type");
            }
        }
    }
    
    /**
     * Class for dealing with OAuth2 authentication
     */
    class HTTPAuthentication_Oauth2 extends HTTPAuthentication {
        protected $type = 'oauth2';
        
        /**
         * Access Token for OAuth2
         * @var string
         */
        protected $accesstoken;
        
        /**
         * Returns Authentication Header Value for OAuth2
         * @return string Authentication Header
         */
        public function build_authenticationheader() {
            return "Authorization: Bearer $this->accesstoken";
        }
        
        /**
         * Sets The Authentication Token 
         * @param string $accesstoken Access Token
         */
        public function set_accesstoken($accesstoken) {
            $this->accesstoken = $accesstoken;
        }
    }
