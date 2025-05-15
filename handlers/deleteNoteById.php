<?php
    $note_Id = substr($notePath, 1);

    $stmt = $db_con->prepare("SELECT id FROM notes WHERE id = ?");
    $stmt->bind_param("s", $note_Id); $stmt->execute();
    $stmt->bind_result($n_id);

    if ($stmt->fetch()) {
        $stmt->close();

        $stmt = $db_con->prepare("DELETE FROM notes WHERE id = ?");
        $stmt->bind_param("s", $n_id); $stmt->execute();
        
        http_response_code(200);
        $response['status'] = 'success';
        $response['message'] = 'Catatan berhasil dihapus';

        echo json_encode($response);
    } else {
        http_response_code(404);
        $response['status'] = 'fail';
        $response['message'] = 'Catatan gagal dihapus. Id tidak ditemukan';

        echo json_encode($response);
    }

    $stmt->close();
?>