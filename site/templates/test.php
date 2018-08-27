<?php 
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $sap = new ConcurFactory();

    $endpoint = $sap->create_endpoint('purchase-order');
    $json = json_decode(file_get_contents($config->paths->site.'test.json'), true);
    $purchaseorders = array_keys($json['ponumbers']);
    echo json_encode($endpoint->add_specificpurchaseorders($purchaseorders));
    //echo json_encode($endpoint->add_receieptsforspecifiedpos($purchaseorders));
    
