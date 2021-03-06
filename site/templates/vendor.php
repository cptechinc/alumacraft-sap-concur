<?php
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    
    $sap = new Dplus\SapConcur\ConcurFactory();
    $endpoint = $sap->create_endpoint('vendor');
    $action = $input->get->text('action');
    $response = array(
        'start' => date('m/d/Y H:i A'),
        'response' => false,
        'end' => ''
    );
    
    if ($input->get->text('apikey') != $config->accesskey) {
        $response = array(
            'error'    => true,
            'messsage' => 'You do not have have access to this function.'
        );
    } else {
        switch ($action) {
            case 'import-vendors':
                $response['response'] = $endpoint->import_concurvendors();
                break;
            case 'update-vendors':
                $response['response'] = $endpoint->batch_vendors();
                break;
            case 'update-vendor':
                $vendorID = $input->get->text('vendorID');
                $response['response'] = $endpoint->send_vendor($vendorID);
                break;
            case 'create-vendors':
                $newvendors = get_vendorIDsnotinsendlog();
                $response['response'] =  $endpoint->$this->create_vendors($newvendors);
                break;
            case 'get-vendor':
                $vendorID = $input->get->text('vendorID');
                $response['response'] = $endpoint->get_vendor($vendorID);
                break;
            default:
                $response['response'] = $endpoint->batch_vendors();
                break;
        }
        $response['end'] = date('m/d/Y H:i A');
    }
    
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
