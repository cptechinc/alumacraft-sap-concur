<?php 
    /* =============================================================
        VENDOR FUNCTIONS
    ============================================================ */
    /**
     * Returns a key-value array for a vendor record
     * @param  string $vendorID Vendor ID
     * @param  bool   $debug    Run in debug? If so, return SQL Query
     * @return array            key-value array for a vendor record
     */
    function get_dbvendor($vendorID, $debug = false) {
        $q = (new QueryBuilder())->table('vendors');
        $q->where('VendorCode', $vendorID);
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
    }
    
    /**
     * Returns a key-value array for a vendor record
     * @param  array  $exclude  Vendor Codes to exclude
     * @param  bool   $debug    Run in debug? If so, return SQL Query
     * @return array            key-value array for a vendor record
     */
    function get_dbvendors($exclude = false, $debug = false) {
        $q = (new QueryBuilder())->table('vendors');
        
        if (!empty($exclude)) {
            $q->where('VendorCode', 'not in', $exclude);
        }
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
    }
    
    /**
     * Returns a key-value array for a vendor record
     * @param  array  $include  Vendor Codes to exclude
     * @param  bool   $debug    Run in debug? If so, return SQL Query
     * @return array            key-value array for a vendor record
     */
    function get_dbvendorsinclude($include = false, $debug = false) {
        $q = (new QueryBuilder())->table('vendors');
        
        if (!empty($include)) {
            $q->where('VendorCode', $include);
        }
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
    }
    /* =============================================================
		PURCHASE ORDER FUNCTIONS
	============================================================ */
    /**
     * Returns the record for Purchase Order header
     * @param  int    $limit How Many Purchase Order Numbers to Return
     * @param  bool   $debug Run in debug? If so, return SQL Query
     * @return array         Purchase Order Numbers in one-dimensional arrays
     */
    function get_dbpurchaseordernbrs($limit = 0, $debug = false) {
        $q = (new QueryBuilder())->table('po_head');
        $q->field($q->expr('DISTINCT(PurchaseOrderNumber)'));
        
        if ($limit) {
            $q->limit($limit);
        }
        
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
    }
    
    /**
     * Returns the record for Purchase Order header
     * @param  string $ponbr Purchase Order Number
     * @param  bool   $debug Run in debug? If so, return SQL Query
     * @return array         Purchase Order header
     */
    function get_dbpurchaseorderheader($ponbr, $debug = false) {
        $q = (new QueryBuilder())->table('po_head');
        $q->where('PurchaseOrderNumber', $ponbr);
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
    }
    
    /**
     * Returns the record for all the Purchase Order detail lines
     * @param  string $ponbr Purchase Order Number
     * @param  bool   $debug Run in debug? If so, return SQL Query
     * @return array         Purchase Order detail lines
     */
    function get_dbpurchaseorderdetails($ponbr, $debug = false) {
        $q = (new QueryBuilder())->table('po_detail');
        $q->where('PurchaseOrderNumber', $ponbr);
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
    }
    
    /* =============================================================
		PURCHASE ORDER RECEIPTS FUNCTIONS
	============================================================ */
    /**
     * Returns all the Purchase Order Numbers available to send receipts for 
     * @param  bool   $debug  Run in debug? If so, return SQL Query
     * @return array          One Dimenisonal array e.g. ('1004', '1005')
     */
    function get_dbdistinctreceiptponbrs($debug = false) {
        $q = (new QueryBuilder())->table('po_receipts');
        $q->field($q->expr("DISTINCT(PurchaseOrderNumber)"));
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
    }
    
    /**
     * Returns a One-dimensional array of all the Purchase Order line numbers
     * have receipts available
     * @param  string $ponbr Purchase Order Number
     * @param  bool   $debug Run in debug? If so, return SQL Query
     * @return array         One-dimensional array of all the Purchase Order line numbers e.g (1, 2, 4)
     */
    function get_dbreceiptslinenbrs($ponbr, $debug = false) {
        $q = (new QueryBuilder())->table('po_receipts');
        $q->field('LineNumber');
        $q->where('PurchaseOrderNumber', $ponbr);
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
    }
    
    /**
     * Returns the receipt record for the Purchase Order Line
     * @param  string $ponbr      Purchase Order Number
     * @param  int    $linenumber Line Number
     * @param  bool   $debug      Run in debug? If so, return SQL Query
     * @return array              Key Value array for that receipt record
     */
    function get_dbreceipt($ponbr, $linenumber, $debug = false) {
        $q = (new QueryBuilder())->table('po_receipts');
        $q->where('PurchaseOrderNumber', $ponbr);
        $q->where('LineNumber', $linenumber);
        $sql = DplusWire::wire('database')->prepare($q->render());
        
        if ($debug) {
            return $q->generate_sqlquery();
        } else {
            $sql->execute($q->params);
            return $sql->fetch(PDO::FETCH_ASSOC);
        }
    }
