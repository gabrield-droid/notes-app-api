<?php
    $note_Id = substr($notePath, 1);

    $defined = FALSE;

    foreach (apcu_fetch('notes') as $note) {
        if ($note['id'] == $note_Id) {
            $defined = TRUE;

            http_response_code(200);
            $response['status'] = 'success';
            $response['data']['note'] = $note;

            echo json_encode($response);
            break;
        }
    }

    if ($defined == FALSE) {
        http_response_code(404);
        $response['status'] = 'fail';
        $response['message'] = 'Catatan tidak ditemukan';

        echo json_encode($response);
    }
?>