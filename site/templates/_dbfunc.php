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
	 * Returns X number of Purchase Order headers
	 * @param  int    $limit How Many Purchase Order Numbers to Return
	 * @param  string $ponbr PO Nbr to start
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Purchase Order Numbers in one-dimensional arrays
	 */
	function get_dbpurchaseordernbrs($limit = 0, $ponbr = '', $debug = false) {
		$q = (new QueryBuilder())->table('po_head');
		$q->field($q->expr('DISTINCT(PurchaseOrderNumber)'));
		
		if (!empty($ponbr)) {
			$q->where('PurchaseOrderNumber', '>', $ponbr);
		} 
		if (!empty($limit)) {
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
	 * @param  string $ponbr  Purchase Order Number to start after
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return array          One Dimenisonal array e.g. ('1004', '1005')
	 */
	function get_dbdistinctreceiptponbrs($limit = 0, $ponbr = '', $debug = false) {
		$q = (new QueryBuilder())->table('po_receipts');
		$q->field($q->expr("DISTINCT(PurchaseOrderNumber)"));
		if (!empty($ponbr)) {
			$q->where('PurchaseOrderNumber', '>', $ponbr);
		} 
		if (!empty($limit)) {
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
	
	/* =============================================================
		INVOICE FUNCTIONS
	============================================================ */
	/**
	 * Returns if Invoice header exists in the database
	 * @param  string $invnbr Invoice Number
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return bool           Does Invoice exist in the header table
	 */
	function does_dbinvoiceexist($invnbr, $debug = false) {
		$q = (new QueryBuilder())->table('ap_invc_head');
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0)'));
		$q->where('InvoiceNumber', $invnbr);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
		}
	}
	
	/**
	 * Inserts an Invoice header in the database
	 * @param  array  $invoice Key-value with columns and their values to set
	 * @param  bool   $debug   Run in debug? If so, return SQL Query
	 * @return int             Insert ID
	 */
	function insert_dbinvoice($invoice, $debug = false) {
		$q = (new QueryBuilder())->table('ap_invc_head');
		$q->mode('insert');
		$columns = array_keys($invoice);
		
		foreach ($invoice as $column => $value) {
			$q->set($column, $value);
		}
		
		$sql = DplusWire::wire('database')->prepare($q->render());
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return DplusWire::wire('database')->lastInsertId();
		}
	}
	
	/**
	 * Updates the Invoice Header in the database
	 * @param  array  $invoice Key-value with columns and their values to set
	 * @param  bool   $debug   Run in debug? If so, return SQL Query
	 * @return int             Row Count of updated rows (1 | 0)
	 */
	function update_dbinvoice($invoice, $debug = false) {
		$q = (new QueryBuilder())->table('ap_invc_head');
		$q->mode('update');
		$columns = array_keys($invoice);
		
		foreach ($invoice as $column => $value) {
			$q->set($column, $value);
		}
		$q->where('InvoiceNumber', $invoice['InvoiceNumber']);
		
		$sql = DplusWire::wire('database')->prepare($q->render());
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->rowCount();
		}
	}
	
	/**
	 * Returns if Invoice Detail Line exists
	 * @param  string $invnbr  Invoice Number
	 * @param  int    $linenbr Detail Line Number
	 * @param  bool   $debug   Run in debug? If so, return SQL Query
	 * @return bool            Does Detail Line eixst?
	 */
	function does_dbinvoicelineexist($invnbr, $linenbr, $debug = false) {
		$q = (new QueryBuilder())->table('ap_invc_detail');
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0)'));
		$q->where('InvoiceNumber', $invnbr);
		$q->where('RequestLineItemNumber', $linenbr);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
		}
	}
	
	/**
	 * Inserts Invnoice Detail Line
	 * @param  string $invnbr      Invoice Number
	 * @param  array  $invoiceline Key-value with columns and their values to set
	 * @param  bool   $debug       Run in debug? If so, return SQL Query
	 * @return int                 Inserted Row ID
	 */
	function insert_dbinvoiceline($invnbr, $invoiceline, $debug = false) {
		$q = (new QueryBuilder())->table('ap_invc_detail');
		$q->mode('insert');
		$q->where('InvoiceNumber', $invnbr);
		
		foreach ($invoiceline as $column => $value) {
			$q->set($column, $value);
		}
		
		$sql = DplusWire::wire('database')->prepare($q->render());
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return DplusWire::wire('database')->lastInsertId();
		}
	}
	
	/**
	 * Updates Invoice Line in the database
	 * @param  string $invnbr      Invoice Number
	 * @param  array  $invoiceline Key-value with columns and their values to set
	 * @param  bool   $debug       Run in debug? If so, return SQL Query
	 * @return int                 Updated rows count (1 | 0)
	 */
	function update_dbinvoiceline($invnbr, $invoiceline, $debug = false) {
		$q = (new QueryBuilder())->table('ap_invc_detail');
		$q->mode('update');
		$columns = array_keys($invoiceline);
		
		foreach ($invoiceline as $column => $value) {
			$q->set($column, $value);
		}
		$q->where('InvoiceNumber', $invnbr);
		$q->where('RequestLineItemNumber', $invoiceline['RequestLineItemNumber']);
		$sql = DplusWire::wire('database')->prepare($q->render());
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->rowCount();
		}
	}
