<?php
    require($functions_dir."/id_generator.php");
    
    $data_in_json = json_decode(file_get_contents('php://input'));

    $stmt = $db_con->prepare("INSERT INTO notes SET 
        id = ?, title = ?, body = ?, tags = ?, createdAt = ?, updatedAt = ?");

    $tags = implode(", ", $data_in_json->{'tags'});
    $id = generate_id(16);

    $createdAt = date_format(date_create(), 'Y-m-d\TH:i:s.v\Z');
    $updatedAt = $createdAt;

    $stmt->bind_param("ssssss", $id, $data_in_json->{'title'}, $data_in_json->{'body'}, $tags, $createdAt, $updatedAt);
    $stmt->execute();

    $stmt->prepare("SELECT id FROM notes WHERE id = ?");
    $stmt->bind_param("s", $id); $stmt->execute();
    $stmt->bind_result($note_id);

    if ($stmt->fetch()) {
        http_response_code(201);
        $response = array("status" => "success", "message" => "Catatan berhasil ditambahkan");
        $response['data']['noteId'] = $id;
        echo json_encode($response);
    } else {
        http_response_code(500);
        $response = array("status" => "fail", "message" => "Catatan gagal ditambahkan");
        echo json_encode($response);
    }

    $stmt->close();
?>