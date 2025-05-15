<?php
    header('Content-type: application/json; charset=utf-8');
    
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        http_response_code(200);
        exit(0);
    }
    
    $root_dir = __DIR__;
    $handlers_dir = $root_dir."/handlers";
    $functions_dir = $root_dir."/functions";
    $db_con_file = $root_dir."/mysql/db_conn.php";
    
    require($db_con_file);

    if (substr($_SERVER['REQUEST_URI'], 0, 6)  == '/notes') {
        $notePath = substr($_SERVER['REQUEST_URI'], 6);
        if ($notePath) {
            if ($notePath[0] == "/") {
                if (substr($notePath, 1)) {
                    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                        require($handlers_dir."/getNoteById.php");
                    } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                        require($handlers_dir."/editNoteById.php");
                    } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
                        require($handlers_dir."/deleteNoteById.php");
                    }
                } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
                    require($handlers_dir."/getAllNotes.php");
                } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
                    require($handlers_dir."/addNote.php");;
                }
            } else {
                http_response_code(404);
        
                $response = array("statusCode" => http_response_code(), "error" => "Not Found", "message" => "Not Found");
                echo json_encode($response);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
            require($handlers_dir."/getAllNotes.php");
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require($handlers_dir."/addNote.php");
        } else {
            http_response_code(404);
        
            $response = array("statusCode" => http_response_code(), "error" => "Not Found", "message" => "Not Found");
            echo json_encode($response);
        }
    } else {
        http_response_code(404);
        
        $response = array("statusCode" => http_response_code(), "error" => "Not Found", "message" => "Not Found");
        echo json_encode($response);
    }

    $db_con->close();
?>