<?php
    ini_set('max_execution_time', 360);
    header('Content-Type: application/json');
    $sap = new ConcurFactory();
    
    $endpoint = $sap->create_endpoint('list-item-inventory');
    $response = array(
        'start' => date('m/d/Y H:i A'),
        'response' => '',
        'end' => ''
    );
    
    $endpoint->set('listID', $page->listid);
    ///$endpoint->set('listID', 'gWvhrLa3BoKEFDlya$sZaCutOS5unrx2PHRg');
    $response['response'] = $endpoint->batch_inventory();
    //$response['response'] = $endpoint->import_concurinventory();
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
