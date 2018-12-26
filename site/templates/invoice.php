<?php
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    
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
        $endpoint = $sap->create_endpoint("invoice");
        $action = $input->get->text('action');
        
        switch ($action) {
            case 'get-invoice':
                $invoicenbr = $input->get->text('invnbr');
                $response['response'] = $endpoint->get_invoice($invoicenbr);
                break;
            case 'get-invoices-after':
                $date = $input->get->text('date');
                $response['response'] = $endpoint->get_invoices_created_after($date);
                break;
            case 'get-invoices-before':
                $date = $input->get->text('date');
                $response['response'] = $endpoint->get_invoicescreatedbefore($date);
                break;
        }
        $response['end'] = date('m/d/Y H:i A');
    }
    echo json_encode($response);
