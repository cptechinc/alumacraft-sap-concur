<?php
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    
    $sap = new Dplus\SapConcur\ConcurFactory();
    $endpoint = $sap->create_endpoint('purchase-order');
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
            case 'send-po':
                $ponbr = $input->get->text('ponbr');
                $response['response'] =  $endpoint->send_purchaseorder($ponbr);
                break;
            case 'create-po':
                $ponbr = $input->get->text('ponbr');
                $response['response'] =  $endpoint->create_purchaseorder($ponbr);
                break;
            case 'send-pos':
                $ponbr = $input->get->text('ponbr');
                if ($input->get->limit) {
                    $limit = $input->get->int('limit');
                    $response['response'] = $endpoint->batch_purchaseorders($limit, $ponbr);
                } else {
                    $response['response'] =  $endpoint->batch_purchaseorders();
                }
                break;
            case 'update-pos':
                $limit = $input->get->int('limit');
                $ponbr = $input->get->text('ponbr');
                $response['response'] = $endpoint->update_purchaseorders($limit, $ponbr);
                break;
            case 'create-pos':
                $limit = $input->get->int('limit');
                $ponbr = $input->get->text('ponbr');
                $response['response'] = $endpoint->create_purchaseorders($limit, $ponbr);
                break;
            case 'extract-pos':
                $endpoint = $sap->create_endpoint("extract-purchase-order");
                $response['response'] = $endpoint->import_concurpurchaseorders();
                break;
            default:
                $response['response'] = $endpoint->batch_purchaseorders(500);
                break;
        }
        $response['end'] = date('m/d/Y H:i A');
    }
    
    echo json_encode($response);
