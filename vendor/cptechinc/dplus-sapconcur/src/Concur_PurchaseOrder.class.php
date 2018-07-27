<?php 
    /**
     * Class to handle dealing with Purchase Orders
     */
    class Concur_PurchaseOrder extends Concur_Endpoint {
        use ConstructAccessToken;
        use StructuredClassTraits;
        
        protected $endpoints = array(
			'purchase-order' => 'https://www.concursolutions.com/api/v3.0/invoice/purchaseorders '
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
                'Name'                => array('dbcolumn' => '', 'required' => false),
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
                    'StateProvince' => array('dbcolumn' => '', 'required' => false)
                ),
                'VendorCode'        => array('dbcolumn' => 'vendorID', 'required' => false),
                'VendorAddressCode' => array('dbcolumn' => 'vendorID', 'required' => false)
            ),
            'detail' => array(
                'Description'              => array('dbcolumn' => '', 'required' => false),
                'ExpenseType'              => array('dbcolumn' => '', 'required' => false),
                'ExternalID'               => array('dbcolumn' => '', 'required' => false),
                'IsReceiptRequired'        => array('dbcolumn' => '', 'required' => false),
                'LineNumber'               => array('dbcolumn' => '', 'required' => false),
                'PurchaseOrderReceiptType' => array('dbcolumn' => '', 'required' => false),
                'Quantity'                 => array('dbcolumn' => '', 'required' => false),
                'UnitPrice'                => array('dbcolumn' => '', 'required' => false)
            )
        );
        
        /**
		 * Sends a POST request to add Purchase Order at Concur
		 * @param  string $ponbr Purchase Order Number
		 * @return array         Response
		 */
		public function create_purchaseorder($ponbr) {
			$purchaseorder =$this->create_purchaseorderheader($ponbr);
            $purchaseorder['LineItem'] = $this->create_purchaseorderdetails($ponbr);
            return $this->post_curl($this->endpoints['purchase-order'], $purchaseorder, $json = true);
		}
        
        /**
         * Sends a PUT request to update Purchase Order at Concur
         * @param  string $ponbr Purchase Order Number
         * @return array         Response
         */
        public function update_purchaseorder($ponbr) {
            $purchaseorder =$this->create_purchaseorderheader($ponbr);
            $purchaseorder['LineItem'] = $this->create_purchaseorderdetails($ponbr);
            return $this->put_curl($this->endpoints['purchase-order'], $purchaseorder, $json = true);
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
    }
