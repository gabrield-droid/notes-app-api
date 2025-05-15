<?php
    http_response_code(200);

    $response['status'] = 'success';
    $stmt = $db_con->prepare("SELECT * FROM notes ORDER by createdAT"); $stmt->execute();
    $stmt->bind_result($n_id, $n_title, $n_body, $n_tags, $n_createdAt, $n_updatedAt);

    $notes = [];
    while ($stmt->fetch()) {
        $tags = explode(",", $n_tags);

        $note = [];
        $note['id'] = $n_id;
        $note['title'] = $n_title;
        $note['createdAt'] = $n_createdAt;
        $note['updatedAt'] = $n_updatedAt;
        $note['tags'] = $tags;
        $note['body'] = $n_body;

        array_push($notes, $note);
    }
    $response['data']['notes'] = $notes;

    echo json_encode($response);

    $stmt->close();
?>