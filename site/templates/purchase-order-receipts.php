<?php
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    
    $sap = new Dplus\SapConcur\ConcurFactory();
    $endpoint = $sap->create_endpoint('purchase-order-receipts');
    $action = $input->get->text('action');
    
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
        switch ($action) {
            case 'send-po-receipts':
                $ponbr = $input->get->text('ponbr');
                $response['response'] =  $endpoint->add_receiptsforpo($ponbr);
                break;
            case 'send-all-receipts':
                $limit = $input->get->int('limit');
                $ponbr = $input->get->text('ponbr');
                $response['response'] = $endpoint->batch_addreceipts($limit, $ponbr);
                break;
        }

        $response['end'] = date('m/d/Y H:i A');
    }
    
    echo json_encode($response);
