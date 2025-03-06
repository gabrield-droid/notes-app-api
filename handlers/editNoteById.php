<?php
    $note_Id = substr($notePath, 1);
    $defined = FALSE;

    $notes = apcu_fetch('notes');
    for ($i=0; $i < count($notes); $i++) { 
        if ($notes[$i]['id'] == $note_Id) {
            $defined = TRUE;
            $data_in_json = json_decode(file_get_contents('php://input'));

            $notes[$i]['title'] = $data_in_json->{'title'};
            $notes[$i]['tags'] = $data_in_json->{'tags'};
            $notes[$i]['body'] = $data_in_json->{'body'};
            $notes[$i]['updatedAt'] = (new DateTime())->format('Y-m-d\TH:i:s.v\Z');

            apcu_store('notes', $notes);

            http_response_code(200);
            $response['status'] = 'success';
            $response['message'] = 'Catatan berhasil diperbarui';

            echo json_encode($response);
            break;
        }
    }

    if ($defined == FALSE) {
        http_response_code(404);
        $response['status'] = 'fail';
        $response['message'] = 'Gagal memperbarui catatan. Id tidak ditemukan';

        echo json_encode($response);
    }
?>