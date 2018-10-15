<?php 
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $sap = new Dplus\SapConcur\ConcurFactory();
    $endpoint = $sap->create_endpoint('authentication');
    $response = array(
        'start' => date('m/d/Y H:i A'),
        'response' => false,
        'end' => ''
    );
$response['response'] = $endpoint->create_authenticationtoken();
$response['authtoken'] = Dplus\SapConcur\Concur_Authentication::$authtoken;
$response['end'] = date('m/d/Y H:i A');
echo json_encode($response);
