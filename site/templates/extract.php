<?php
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    
    $sap = new Dplus\SapConcur\ConcurFactory();
    $endpoint = $sap->create_endpoint("extract-$page->name");
    $response = array(
        'start' => date('m/d/Y H:i A'),
        'response' => '',
        'end' => ''
    );
    
    if ($input->get->text('apikey') != $config->accesskey) {
        $response = array(
            'error'    => true,
            'messsage' => 'You do not have have access to this function.'
        );
    } else {
        $response['response'] = $endpoint->import_concurpurchaseorders();
        $response['end'] = date('m/d/Y H:i A');
    }
    
    echo json_encode($response);
