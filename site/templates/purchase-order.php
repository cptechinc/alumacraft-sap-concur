<?php
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $sap = new Dplus\SapConcur\ConcurFactory();
    
    $endpoint = $sap->create_endpoint('purchase-order');
    $response = array(
        'start' => date('m/d/Y H:i A'),
        'response' => '',
        'end' => ''
    );
    
    if ($input->get->ponbr) {
        $ponbr = $input->get->text('ponbr');
        
        if ($input->get->limit) {
            $limit = $input->get->int('limit');
            $response['response'] = $endpoint->batch_purchaseorders($limit, $ponbr);
        } else {
            $response['response'] =  $endpoint->send_purchaseorder($ponbr);
        }
    } else {
        $response['response'] = $endpoint->batch_purchaseorders();
    }
    
    
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
