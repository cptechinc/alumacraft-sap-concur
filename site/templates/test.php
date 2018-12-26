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
$response['response'] = get_dbpurchaseordernbrsinsendlog(100, '312888', true);
$response['end'] = date('m/d/Y H:i A');
echo json_encode($response);
