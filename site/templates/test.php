<?php 
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $endpoint = $sap->create_endpoint('purchase-order-receipts');
    $response = array('start' => date('m/d/Y H:i A'));
    $response['response'] = $endpoint->batch_addreceipts(1000, '318022');
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
