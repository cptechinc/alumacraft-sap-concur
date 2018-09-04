<?php 
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $endpoint = $sap->create_endpoint('purchase-order');
    $ponumbers = array_keys(json_decode(file_get_contents($config->paths->site."test.json"), true));
    $response = array('start' => date('m/d/Y H:i A'));
    $response['response'] = $endpoint->add_specificpurchaseorders($ponumbers);
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
