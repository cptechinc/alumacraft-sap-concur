<?php 
    /**
     * Class to handle dealing with Purchase Orders
     */
    class Concur_PurchaseOrder extends Concur_Endpoint {
        use ConstructAccessToken;
        use StructuredClassTraits;
        
        protected $endpoints = array(
			'purchase-order' => 'https://www.concursolutions.com/api/v3.0/invoice/purchaseorders'
		);
        
        /**
         * Structure of Purchase Order
         * @var array
         */
        protected $structure = array(
            'header' => array(
                'BillToAddress' => array(
                    'Address1'          => array('dbcolumn' => 'billtoAddress1', 'required' => false),
                    'Address2'          => array('dbcolumn' => 'billtoAddress2', 'required' => false),
                    'Address3'          => array('dbcolumn' => 'billtoAddress3', 'required' => false),
                    'City'              => array('dbcolumn' => 'billtoCity', 'required' => false),
                    'CountryCode'       => array('dbcolumn' => 'billtoCountryCode', 'required' => false),
                    'ExternalID'        => array('dbcolumn' => 'billtoID', 'required' => false),
                    'Name'              => array('dbcolumn' => 'billtoName', 'required' => false),
                    'PostalCode'        => array('dbcolumn' => 'billtoZip', 'required' => false),
                    'StateProvince'     => array('dbcolumn' => 'billtoState', 'required' => false)
                ),
                'CurrencyCode'        => array('dbcolumn' => '', 'required' => false),
                'OrderDate'           => array('dbcolumn' => '', 'required' => false, 'format' => 'date', 'date-format' => 'Y-m-d'),
                'ID'                  => array('dbcolumn' => 'PurchaseOrderNumber', 'required' => false),
                'LedgerCode'          => array('dbcolumn' => '', 'required' => false),
                'Name'                => array('dbcolumn' => 'PurchaseOrderNumber', 'required' => false),
                'PolicyExternalID'    => array('dbcolumn' => '', 'required' => false),
                'PurchaseOrderNumber' => array('dbcolumn' => '', 'required' => false),
                'ShipToAddress' => array(
                    'Address1'      => array('dbcolumn' => 'shiptoAddress1', 'required' => false),
                    'Address2'      => array('dbcolumn' => 'shiptoAddress2', 'required' => false),
                    'City'          => array('dbcolumn' => 'shiptoCity', 'required' => false),
                    'CountryCode'   => array('dbcolumn' => 'shiptoCountryCode', 'required' => false),
                    'ExternalID'    => array('dbcolumn' => 'shiptoID', 'required' => false),
                    'Name'          => array('dbcolumn' => 'shiptoName', 'required' => false),
                    'PostalCode'    => array('dbcolumn' => 'shiptoZip', 'required' => false),
                    'State'         => array('dbcolumn' => 'shiptoState', 'required' => false),
                    'StateProvince' => array('dbcolumn' => 'shiptoState', 'required' => false)
                ),
                'VendorCode'        => array('dbcolumn' => 'vendorID', 'required' => false),
                'VendorAddressCode' => array('dbcolumn' => 'vendorID', 'required' => false)
            ),
            'detail' => array(
                'AccountCode'              => array('dbcolumn' => 'ExpenseType', 'required' => false),
                'Description'              => array('dbcolumn' => '', 'required' => false),
                'ExternalID'               => array('dbcolumn' => '', 'required' => false),
                'IsReceiptRequired'        => array('dbcolumn' => '', 'required' => false),
                'LineNumber'               => array('dbcolumn' => '', 'required' => false),
                'PurchaseOrderReceiptType' => array('dbcolumn' => '', 'required' => false, 'default' => 'WQTY'),
                'Quantity'                 => array('dbcolumn' => '', 'required' => false),
                'UnitPrice'                => array('dbcolumn' => '', 'required' => false)
            )
        );
        
        /**
         * Sends GET Request to retreive Purchase Order
         * @param  string $ponbr Purchase Order Number
         * @return array         Response from Concur
         */
        public function get_purchaseorder($ponbr) {
            $url = $this->endpoints['purchase-order'] . "/$ponbr";
            return $this->get_curl($url);
        }
        
        /**
         * Verifies if Purchase Order Exists at Concur
         * @param  string $ponbr Purchase Order Number
         * @return bool          Does Purchase Order Number exist?
         */
        public function does_poexist($ponbr) {
            $response = $this->get_purchaseorder($ponbr);
            return (isset($response['PurchaseOrderNumber'])) ? true : false;
        }
        
        /**
		 * Sends a POST request to add Purchase Order at Concur
		 * @param  string $ponbr Purchase Order Number
		 * @return array         Response
		 */
		public function create_purchaseorder($ponbr) {
			$purchaseorder = $this->create_purchaseorderheader($ponbr);
            $purchaseorder['LineItem'] = $this->create_purchaseorderdetails($ponbr);
            $this->response =  $this->post_curl($this->endpoints['purchase-order'], $purchaseorder, $json = true);
            $this->process_response();
            return $this->response;
		}
        
        /**
         * Sends a PUT request to update Purchase Order at Concur
         * @param  string $ponbr Purchase Order Number
         * @return array         Response
         */
        public function update_purchaseorder($ponbr) {
            $purchaseorder = $this->create_purchaseorderheader($ponbr);
            $purchaseorder['LineItem'] = $this->create_purchaseorderdetails($ponbr);
            $this->response = $this->put_curl($this->endpoints['purchase-order'], $purchaseorder, $json = true);
            $this->process_response();
            return $this->response;
        }
        
        /**
         * Verifies if Purchase Order exits at Concur 
         * If it exists then it updates the Purchase Order
         * IF not, it will create the Purchase Order
         * @param  string $ponbr Purchase Order Number
         * @return void
         */
        public function send_purchaseorder($ponbr) {
            if ($this->does_poexist($ponbr)) {
                $this->update_purchaseorder($ponbr);
            } else {
                $this->create_purchaseorder($ponbr);
            }
        }
        
        /**
         * Process a batch of Purchase Orders and handle the update / create for each
         * @param  int    $limit Number of POs to do, If 0, Then There is no limit
         * @return array        Response for each PO Number Keyed by Purchase Order Number
         */
        public function batch_purchaseorders($limit = 0) {
            $purchaseorders = get_dbpurchaseordernbrs($limit);
            $response = array();
            
            foreach ($purchaseorders as $ponbr) {
                $this->send_purchaseorder($ponbr);
                $response[$ponbr] = $this->response;
            }
            $this->response = $response;
            return $this->response;
        }
        
        
        /**
         * Gets Purchase Order header from Database and apply it to the structure needed
         * @param  string $ponbr Purchase Order Number
         * @return array         Array in the Header array Structure
         */
        protected function create_purchaseorderheader($ponbr) {
            $purchaseorder = get_dbpurchaseorderheader($ponbr);
            return $this->create_sectionarray($this->structure['header'], $purchaseorder);
        }
        
        /**
         * Gets the Purchase Order Details and foreach one puts them in the detail
         * structure needed then returns an array of all of them
         * @param  string $ponbr Purchase Order Number
         * @return array         Details in the detail array Structure
         */
        protected function create_purchaseorderdetails($ponbr) {
            $lines = array();
            $details = get_dbpurchaseorderdetails($ponbr);
            
            foreach ($details as $detail) {
                $line = $this->create_sectionarray($this->structure['detail'], $detail);
                $line['Allocation'] = array(array('Amount' => $detail['LineTotal']));
                $lines[] = $line;
            }
            return $lines;
        }
        
        /**
         * Processes Response and logs Errors if needed
         * @return void
         */
        protected function process_response() {
            $this->response['Status'] = isset($this->response['Status']) ? $this->response['Status'] : '';
            
            if (isset($this->response['error']) || $this->response['Status'] == 'FAILURE') {
                $error = !empty($this->response['ErrorCode']) ? "ErrorCode: " . $this->response['ErrorCode'] . " -> " : '';
                $error .= !empty($this->response['ErrorMessage']) ? $this->response['ErrorMessage'] : $this->response['Message'];
                $error .= " -> ";
                $error .= !empty($this->response['FieldCode']) ? "FieldCode: " . $this->response['FieldCode'] : '';
                $this->log_error($error);
            } elseif (strpos(strtolower($response['Message']), strtolower('Purchase Order Cannot be updated as it does not exist in system')) !== false) {
                $error = $response['Message'];
                $this->log_error($error);
            }
        }
    }
