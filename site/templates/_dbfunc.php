<?php 
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
    
    function get_dbpurchaseorder($ponbr, $debug = false) {
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
