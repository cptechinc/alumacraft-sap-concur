<?php 
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $vendorendpoint = $sap->create_endpoint('purchase-order');
    echo json_encode($vendorendpoint->create_purchaseorder('1500'));
