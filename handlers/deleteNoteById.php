<?php
    $note_Id = substr($notePath, 1);
    $defined = FALSE;

    $notes = apcu_fetch('notes');
    for ($i=0; $i < count($notes); $i++) { 
        if ($notes[$i]['id'] == $note_Id) {
            $defined = TRUE;
            array_splice($notes, $i, 1);

            apcu_store('notes', $notes);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Catatan berhasil dihapus';

            echo json_encode($response);
            break;
        }
    }

    if ($defined == FALSE) {
        http_response_code(404);
        $response['status'] = 'fail';
        $response['message'] = 'Catatan gagal dihapus. Id tidak ditemukan';

        echo json_encode($response);
    }
?>