<?php
	use Dplus\ProcessWire\DplusWire;
	use Dplus\Base\QueryBuilder;
	
	
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
		$q->where($q->expr('TRIM(VendorCode)'), $vendorID);
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
	function get_dbvendoridsexclude($exclude = false, $debug = false) {
		$q = (new QueryBuilder())->table('vendors');
		
		if (!empty($exclude)) {
			$q->where($q->expr('TRIM(VendorCode)'), 'not in', $exclude);
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
			$q->where($q->expr('TRIM(VendorCode)'), $include);
		}
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	
	
	/**
	 * Returns if there's a sendlog_vendor record for vendor
	 * @param  string $vendorID Vendor ID to check for
	 * @param  bool   $debug    Run in debug? If so, return SQL Query
	 * @return bool             Does Item have a send log record?
	 */
	function does_vendorhavesendlog($vendorID, $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_vendor');
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0)'));
		$q->where('VendorCode', $vendorID);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
		}
	}
	
	/**
	 * Updates the sendlog_vendor table when an vendor has been sent to concur to be updated
	 * @param  string $vendorID Vendor ID
	 * @param  string $date     MySQL datetime MM-DD-YYYY HH:MM:SS
	 * @param  bool   $debug    Run in debug? If so, return SQL Query
	 * @return string           Updated rows count (1 | 0)
	 */
	function update_sendlogvendor($vendorID, $date, $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_vendor');
		$q->mode('update');
		$q->set('updated', $date);
		$q->where('VendorCode', $vendorID);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->rowCount();
		}
	}
	
	/**
	 * Inserts a record for the sendlog_item_list table when the item has been sent to Concur
	 * @param  string $vendorID Vendor ID / Code
	 * @param  string $date     MySQL datetime MM-DD-YYYY HH:MM:SS
	 * @param  bool   $debug    Run in debug? If so, return SQL Query
	 * @return string           Last Insert ID
	 */
	function insert_sendlogvendor($vendorID, $date, $debug = false) {
		$date = date('Y-m-d H:i:s', strtotime($date));
		$q = (new QueryBuilder())->table('sendlog_vendor');
		$q->mode('insert');
		$q->set('VendorCode', $vendorID);
		$q->set('updated', $date);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return DplusWire::wire('database')->lastInsertId();
		}
	}
	
	/**
	 * Returns an Array of Vendor Codes (IDs) from the sendlog
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         ItemIDs
	 */
	function get_sendlogvendors($debug = false) {
		$q = (new QueryBuilder())->table('sendlog_vendor');
		$q->field('VendorCode');
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
	}
	
	function get_vendorIDsinsendlog($updatedafter = '', $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_vendor');
		$q->field('VendorCode');
		if (!empty($updatedafter)) {
			$updatedafter = date('Y-m-d', strtotime($updatedafter));
			$q->where($q->expr('DATE(updated)'), '<', $updatedafter);
			$q->where($q->expr('DATE(date)'), '>', $updatedafter);
		}
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
	}
	
	function get_vendorIDsnotinsendlog($debug = false) {
		$logquery  = (new QueryBuilder())->table('sendlog_vendor');
		$logquery->field('VendorCode');
		
		$q = (new QueryBuilder())->table('vendors');
		$q->field('VendorCode');
		$q->where($q->expr('TRIM(VendorCode)'), 'not in', $logquery);
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
	 * Returns Purchase Order Numbers that aren't in the sendlog
	 * @param  int    $limit Number of Records to return
	 * @param  string $ponbr PO Number to start after
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Purchase Order Numbers in one-dimensional arrays
	 */
	function get_dbpurchaseordernbrsnotinsendlog($limit = 0, $ponbr = '', $debug = false) {
		$logquery  = (new QueryBuilder())->table('sendlog_po');
		$logquery->field('PurchaseOrderNumber');
		
		$q = (new QueryBuilder())->table('po_head');
		$q->field($q->expr('DISTINCT(PurchaseOrderNumber) as PurchaseOrderNumber'));
		$q->where('PurchaseOrderNumber', 'not in', $logquery);
		if (!(empty($limit))) {
			$q->limit($limit);
		}
		if (!empty($ponbr)) {
			$q->where('PurchaseOrderNumber', '>', $ponbr);
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
	 * Returns the number of Purchase Order Numbers that aren't in the sendlog
	 * @param  string $ponbr PO Number to start after
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Purchase Order Numbers in one-dimensional arrays
	 */
	function count_dbpurchaseordernbrsnotinsendlog($ponbr = '', $debug = false) {
		$logquery  = (new QueryBuilder())->table('sendlog_po');
		$logquery->field('PurchaseOrderNumber');
		
		$q = (new QueryBuilder())->table('po_head');
		$q->field($q->expr('COUNT(PurchaseOrderNumber)'));
		$q->where('PurchaseOrderNumber', 'not in', $logquery);
		if (!empty($ponbr)) {
			$q->where('PurchaseOrderNumber', '>', $ponbr);
		} 
		
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
		}
	}
	
	/**
	 * Returns Purchase Order Numbers that are in the sendlog
	 * // NOTE checks that Purchase Order exists in po_head
	 * @param  int    $limit Number of Records to return
	 * @param  string $ponbr PO Number to start after
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Purchase Order Numbers in one-dimensional arrays
	 */
	function get_dbpurchaseordernbrsinsendlog($limit = 0, $ponbr = '', $debug = false) {
		$q  = (new QueryBuilder())->table('sendlog_po');
		$q->field('PurchaseOrderNumber');
		$poquery = (new QueryBuilder())->table('po_head');
		$poquery->field('PurchaseOrderNumber');
		$q->where('PurchaseOrderNumber', 'in', $poquery);
		if (!empty($ponbr)) {
			$q->where('PurchaseOrderNumber', '>', $ponbr);
		}
		if (!(empty($limit))) {
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
	 * Returns the number of Purchase Order Numbers that are in the sendlog
	 * @param  string $ponbr PO Number to start after
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Purchase Order Numbers in one-dimensional arrays
	 */
	function count_dbpurchaseordernbrsinsendlog($ponbr = '', $debug = false) {
		$q  = (new QueryBuilder())->table('sendlog_po');
		$q->field($q->expr('COUNT(*)'));
		if (!empty($ponbr)) {
			$q->where('PurchaseOrderNumber', '>', $ponbr);
		} 
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
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
		$q->where('LineTotal', '!=', '0');
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	
	/**
	 * Returns if there's a sendlog_po record for X item
	 * @param  string $ponbr  Purchase Order Number
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return bool           Does Item have a send log record?
	 */
	function does_pohavesendlog($ponbr, $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_po');
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0)'));
		$q->where('PurchaseOrderNumber', $ponbr);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
		}
	}
	
	/**
	 * Updates the sendlog_po table when a Purchase Order has been sent to concur to be updated
	 * @param  string $ponbr  Purchase Order Number
	 * @param  string $date   MySQL datetime MM-DD-YYYY HH:MM:SS
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return string         Updated rows count (1 | 0)
	 */
	function update_sendlogpo($ponbr, $date, $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_po');
		$q->mode('update');
		$q->set('updated', $date);
		$q->where('PurchaseOrderNumber', $ponbr);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->rowCount();
		}
	}
	
	/**
	 * Inserts a record for the sendlog_po table when the po has been sent to Concur
	 * @param  string $ponbr  Purchase Order Number
	 * @param  string $date   MySQL datetime MM-DD-YYYY HH:MM:SS
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return string         Last Insert ID
	 */
	function insert_sendlogpo($ponbr, $date, $debug = false) {
		$date = date('Y-m-d H:i:s', strtotime($date));
		$q = (new QueryBuilder())->table('sendlog_po');
		$q->mode('insert');
		$q->set('PurchaseOrderNumber', $ponbr);
		$q->set('updated', $date);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return DplusWire::wire('database')->lastInsertId();
		}
	}
	
	/* =============================================================
		PURCHASE ORDER RECEIPTS FUNCTIONS
	============================================================ */
	/**
	 * Returns all the Purchase Order Numbers available to send receipts for 
	 * @param  int    $limit How many Purchase Order Numbers to return
	 * @param  string $ponbr  Purchase Order Number to start after
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return array          One Dimenisonal array e.g. ('1004', '1005')
	 */
	function get_dbdistinctreceiptponbrs($limit = 0, $ponbr = '', $debug = false) {
		$detail_query = (new QueryBuilder())->table('po_detail');
		$detail_query->field('PurchaseOrderNumber');
		$detail_query->field('LineNumber');
		
		$q = (new QueryBuilder())->table('po_receipts');
		$q->field($q->expr("DISTINCT(PurchaseOrderNumber)"));
		$q->where('(PurchaseOrderNumber, LineNumber)', 'in', $detail_query);
		
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
		$detail_query = (new QueryBuilder())->table('po_detail');
		$detail_query->field('LineNumber');
		$detail_query->where('PurchaseOrderNumber', $ponbr);
		
		$q = (new QueryBuilder())->table('po_receipts');
		$q->field('LineNumber');
		$q->where('PurchaseOrderNumber', $ponbr);
		$q->where('LineNumber', 'in', $detail_query);
		
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
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0) AS count'));
		$q->where('InvoiceNumber', $invnbr);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->render_sqlquery();
		} else {
			$sql->execute($q->params);
			return boolval($sql->fetchColumn());
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
		$invnbr = $invoice['InvoiceNumber'];
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
		$invnbr = $invoice['InvoiceNumber'];
		unset($invoice['InvoiceNumber']);
		
		foreach ($invoice as $column => $value) {
			$q->set($column, $value);
		}
		$q->where('InvoiceNumber', "$invnbr");
		
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
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0) AS count'));
		$q->where('InvoiceNumber', $invnbr);
		$q->where('RequestLineItemNumber', $linenbr);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return boolval($sql->fetchColumn());
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
	
	/* =============================================================
		ITEM MASTER FUNCTIONS
	==============================================================*/
	/**
	 * Returns if there's a sendlog_item_list record for X item
	 * @param  string $itemID Item ID to check for
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return bool           Does Item have a send log record?
	 */
	function does_itemhavesendlog($itemID, $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_item_list');
		$q->field($q->expr('IF(COUNT(*) > 0, 1, 0)'));
		$q->where('ItemID', $itemID);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchColumn();
		}
	}
	
	/**
	 * Updates the sendlog_item_list table when an item has been sent to concur to be updated
	 * @param  string $itemID Item ID
	 * @param  string $date   MySQL datetime MM-DD-YYYY HH:MM:SS
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return string         Updated rows count (1 | 0)
	 */
	function update_sendlogitem($itemID, $date, $debug = false) {
		$q = (new QueryBuilder())->table('sendlog_item_list');
		$q->mode('update');
		$q->set('updated', $date);
		$q->where('ItemID', $itemID);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->rowCount();
		}
	}
	
	/**
	 * Inserts a record for the sendlog_item_list table when the item has been sent to Concur
	 * @param  string $itemID Item ID
	 * @param  string $date   MySQL datetime MM-DD-YYYY HH:MM:SS
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return string         Last Insert ID
	 */
	function insert_sendlogitem($itemID, $date, $debug = false) {
		$date = date('Y-m-d H:i:s', strtotime($date));
		$q = (new QueryBuilder())->table('sendlog_item_list');
		$q->mode('insert');
		$q->set('ItemID', $itemID);
		$q->set('updated', $date);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return DplusWire::wire('database')->lastInsertId();
		}
	}
	
	/**
	 * Returns an Array of ItemIDs
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         ItemIDs
	 */
	function get_sendlogitemids($debug = false) {
		$q = (new QueryBuilder())->table('sendlog_item_list');
		$q->field('ItemID');
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute();
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
	}
	
	/**
	 * Returns Item List
	 * @param  int    $limit How Many Items to return
	 * @param  string $start Start after X
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Item List
	 */
	function get_itemlist($limit = 0, $start = '', $debug = false) {
		$q = (new QueryBuilder())->table('item_list');
		
		if (!empty($start)) {
			$q->where('ItemID', '>', $start);
		}
		
		if ($limit) {
			$q->limit($limit);
		}
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_ASSOC);
		}
	}
	
	/**
	 * Returns Items in the Sendlog
	 * @param  string $updatedafter  Date to get items updated after X date
	 * @param  bool   $debug         Run in debug? If so, return SQL Query
	 * @return array                 Items whose item IDs are found in the sendlog
	 */
	function get_itemidsinsendlog($updatedafter = '', $debug = false) {
		$q = (new QueryBuilder())->table('item_list');
		$q->field('item_list.ItemID');
		$q->join('sendlog_item_list.ItemID', 'ItemID');
		if (!empty($updatedafter)) {
			$updatedafter = date('Y-m-d', strtotime($updatedafter));
			$q->where($q->expr('DATE(sendlog_item_list.updated)'), '<', $updatedafter);
			$q->where($q->expr('DATE(item_list.date)'), '>', $updatedafter);
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
	 * Returns Items not in the Sendlog
	 * @param  bool   $debug Run in debug? If so, return SQL Query
	 * @return array         Items whose item IDs are not found in the sendlog
	 */
	function get_itemidsnotinsendlog($debug = false) {
		$logquery  = (new QueryBuilder())->table('sendlog_item_list');
		$logquery->field('ItemID');
		
		$q = (new QueryBuilder())->table('item_list');
		$q->field('ItemID');
		$q->where('ItemID', 'not in', $logquery);
		$q->limit(27000);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetchAll(PDO::FETCH_COLUMN);
		}
	}
	
	/**
	 * Returns One Item out of the item_list table
	 * @param  string $itemID Item ID
	 * @param  bool   $debug  Run in debug? If so, return SQL Query
	 * @return array          Item
	 */
	function get_item($itemID, $debug = false) {
		$q = (new QueryBuilder())->table('item_list');
		$q->field('ItemID');
		$q->field('ItemDescription');
		$q->where('ItemID', $itemID);
		$q->limit(1);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return $sql->fetch(PDO::FETCH_ASSOC);
		}
	}
	
	
	function insert_errorlog($endpoint, $message, $date, $debug = false) {
		$q = (new QueryBuilder())->table('log_error');
		$q->mode('insert');
		$q->set('date', $date);
		$q->set('endpoint', $endpoint);
		$q->set('message', $message);
		$sql = DplusWire::wire('database')->prepare($q->render());
		
		if ($debug) {
			return $q->generate_sqlquery();
		} else {
			$sql->execute($q->params);
			return boolval(DplusWire::wire('database')->lastInsertId());
		}
	}
