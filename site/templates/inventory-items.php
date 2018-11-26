<?php
    ini_set('max_execution_time', 480);
    header('Content-Type: application/json');
    $sap = new Dplus\SapConcur\ConcurFactory();
    $endpoint = $sap->create_endpoint('list-item-inventory');
    $action = $input->get->text('action');
    $response = array(
        'start' => date('m/d/Y H:i A'),
        'response' => '',
        'end' => ''
    );
    
    $endpoint->set('listID', $page->listid);
    switch ($action) {
        case 'import-items':
            $response['response'] = $endpoint->import_concurinventory();
            break;
        case 'update-items':
            $response['response'] = $endpoint->batch_inventory();
            break;
        case 'update-item':
            $itemID = $input->get->text('itemID');
            $response['response'] = $endpoint->send_inventoryitem($itemID);
            break;
        case 'get-item':
            $vendorID = $input->get->text('itemID');
            $response['response'] = $endpoint->get_vendor($vendorID);
            break;
        default:
            $response['response'] = $endpoint->batch_inventory();
            break;
    }
    $response['end'] = date('m/d/Y H:i A');
    echo json_encode($response);
