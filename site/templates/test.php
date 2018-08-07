<?php 
    header('Content-Type: application/json');
    $sap = new ConcurFactory();

    $endpoint = $sap->create_endpoint('vendor');
    echo json_encode($endpoint->batch_vendors());
