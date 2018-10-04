<?php 
    ini_set('max_execution_time', 240);
    header('Content-Type: application/json');
    $dplus = new Dplus\Base\Curl();
    echo json_encode(get_declared_classes());
