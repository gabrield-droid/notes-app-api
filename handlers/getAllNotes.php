<?php
    http_response_code(200);

    $response['status'] = 'success';
    $response['data']['notes'] = apcu_fetch('notes');

    echo json_encode($response);
?>