<?php 
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $vendorendpoint = $sap->create_endpoint('purchase-order');
    echo json_encode($vendorendpoint->create_purchaseorder('00301641'));
    $vendorendpoint->log_error("Failed to create purchase order 00301641");
