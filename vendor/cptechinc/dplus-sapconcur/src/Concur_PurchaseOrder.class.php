<?php 
    /**
     * Class to handle dealing with Purchase Orders
     */
    class Concur_PurchaseOrder extends Concur_Endpoint {
        use ConstructAccessToken;
        use StructuredClassTraits;
        
        protected $endpoints = array(
			'purchase-orders' => 'https://www.concursolutions.com/api/v3.0/invoice/purchaseorders '
		);
        
        protected $structure = array(
            'sections' => array(
                'header' => array('BillToAddress', 'ShipToAddress')
            ),
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
		 * Sends a PUT request to add Vendor at Concur
		 * @param  string $ponbr Vendor ID to use to load from database
		 * @return array            Response
		 */
		public function create_purchaseorder($ponbr) {
			return $this->create_purchaseorderheader($ponbr);
		}
        
        protected function create_purchaseorderheader($ponbr) {
            $purchaseorder = get_dbpurchaseorder($ponbr);
            
            return $this->create_sectionarray($this->structure['header'], $purchaseorder);
        }
        
        
    }
