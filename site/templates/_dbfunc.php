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
