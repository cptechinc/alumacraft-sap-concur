<?php 
    class Concur_PurchaseOrderReceipts extends Concur_Endpoint {
        use ConstructAccessToken;
        use StructuredClassTraits;
        
        protected $endpoints = array(
			'receipts' => 'https://www.concursolutions.com/api/v3.0/invoice/purchaseorderreceipts'
		);
        
        /**
         * Structure of Purchase Order
         * @var array
         */
        protected $structure = array(
            'header' => array(
                'PurchaseOrderNumber' => array('dbcolumn' => '', 'required' => false),
                'LineNumber'          => array('dbcolumn' => '', 'required' => false),
                'LineItemExternalID'  => array('dbcolumn' => '', 'required' => false),
                'ReceivedDate'        => array('dbcolumn' => '', 'required' => false, 'format' => 'date', 'date-format' => 'Y-m-d'),
                'ReceivedQuantity'    => array('dbcolumn' => '', 'required' => false)
            )
        );
        
        /**
         * Adds all the receipts necessary for one Purchase Order
         * @param string $ponbr Purchase Order Number
         */
        public function add_receiptsforpo($ponbr) {
            $receiptlines = get_dbreceiptslinenbrs($ponbr);
            
            foreach ($receiptlines as $linenumber) {
                $this->add_receipt($ponbr, $linenumber);
            }
        }
        
        /**
         * Sends PUT Request to update a Purchase Order with receipt data 
         * for a Purchase Order Line
         * @param string $ponbr      Purchase Order Number
         * @param int    $linenumber Line Number
         */
        public function add_receipt($ponbr, $linenumber) {
            $receipt = get_dbreceipt($ponbr, $linenumber);
            $data = $this->create_sectionarray($this->structure['header'], $receipt);
            return $this->put_curl($this->endpoints['receipt'], $data, $json = true);
        }
        
        /**
         * Gets all the PO Numbers
         * @return [type] [description]
         */
        public function batch_addreceipts() {
            $purchaseordernumbers = get_dbdistinctreceiptponbrs();
            
            foreach ($purchaseordernumbers as $ponbr) {
                $this->add_receiptsforpo($ponbr);
            }
        }
    }
