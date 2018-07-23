<?php 
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $vendorendpoint = $sap->create_endpoint('vendor');
    echo json_encode($vendorendpoint->get_vendor('8628'));
