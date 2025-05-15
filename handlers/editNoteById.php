<?php
    $note_Id = substr($notePath, 1);

    $stmt = $db_con->prepare("SELECT id FROM notes WHERE id = ?");
    $stmt->bind_param("s", $note_Id); $stmt->execute();
    $stmt->bind_result($n_id);

    if ($stmt->fetch()) {
        $stmt->close();
        $data_in_json = json_decode(file_get_contents('php://input'));

        $tags = implode(", ", $data_in_json->{'tags'});
        $updatedAt = date_format(date_create(), 'Y-m-d\TH:i:s.v\Z');

        $stmt = $db_con->prepare("UPDATE notes SET
            title = ?, tags = ?, body = ?, updatedAt = ?
        WHERE id = ?");

        $stmt->bind_param("sssss", $data_in_json->{'title'}, $tags, $data_in_json->{'body'}, $updatedAt, $n_id);
        $stmt->execute();

        http_response_code(200);
        $response['status'] = 'success';
        $response['message'] = 'Catatan berhasil diperbarui';

        echo json_encode($response);
    } else {
        http_response_code(404);
        $response['status'] = 'fail';
        $response['message'] = 'Gagal memperbarui catatan. Id tidak ditemukan';

        echo json_encode($response);
    }

    $stmt->close();
?>