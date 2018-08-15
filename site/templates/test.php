<?php 
    header('Content-Type: application/json');
    $sap = new ConcurFactory();

    $endpoint = $sap->create_endpoint('purchase-order-receipts');
    echo json_encode($endpoint->batch_addreceipts());
