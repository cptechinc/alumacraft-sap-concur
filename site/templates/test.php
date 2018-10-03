<?php 
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $endpoint = $sap->create_endpoint('authentication');
    $response = array('start' => date('m/d/Y H:i A'));
    $response['response'] = $endpoint->create_authenticationtoken();
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
