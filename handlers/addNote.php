<?php
    require("$functions_dir/id_generator.php");
    
    $data_in_json = json_decode(file_get_contents('php://input'));

    $newNote = []; 
    $newNote['title'] = $data_in_json->{'title'};
    $newNote['tags'] = $data_in_json->{'tags'};
    $newNote['body'] = $data_in_json->{'body'};

    $newNote['id'] = generate_id(16);

    $newNote['createdAt'] = (new DateTime())->format('Y-m-d\TH:i:s.v\Z');
    $newNote['updatedAt'] = $newNote['createdAt'];

    $notes = apcu_fetch('notes');
    array_push($notes, $newNote);
    apcu_store('notes', $notes);

    $isSuccess = FALSE;
    foreach (apcu_fetch('notes') as $note) {
        if ($note['id'] == $newNote['id']) {
            $isSuccess = TRUE;
            break;
        }
    }

    $response = array("status" => "fail", "message" => "Catatan gagal ditambahkan");

    if ($isSuccess) {
        http_response_code(201);
        $response['status'] = 'success';
        $response['message'] = 'Catatan berhasil ditambahkan';
        $response['data']['noteId'] = $newNote['id'];
        echo json_encode($response);
    } else {
        http_response_code(500);
        echo json_encode($response);
    }
?>