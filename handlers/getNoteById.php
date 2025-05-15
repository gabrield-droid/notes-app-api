<?php
    $note_Id = substr($notePath, 1);

    $stmt = $db_con->prepare("SELECT * FROM notes WHERE id = ?");
    $stmt->bind_param("s", $note_Id); $stmt->execute();
    $stmt->bind_result($n_id, $n_title, $n_body, $n_tags, $n_createdAt, $n_updatedAt);

    if ($stmt->fetch()) {
        $tags = explode(", ", $n_tags);

        $note = [];
        $note['id'] = $n_id;
        $note['title'] = $n_title;
        $note['createdAt'] = $n_createdAt;
        $note['updatedAt'] = $n_updatedAt;
        $note['tags'] = $tags;
        $note['body'] = $n_body;

        http_response_code(200);
        $response['status'] = 'success';
        $response['data']['note'] = $note;

        echo json_encode($response);
    } else {
        http_response_code(404);
        $response['status'] = 'fail';
        $response['message'] = 'Catatan tidak ditemukan';

        echo json_encode($response);
    }

    $stmt->close();
?>