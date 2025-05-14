<?php
    require __DIR__ . "/db_config.php";
    
    $host = "localhost";
    $user = DB_USER;
    $pass = DB_PASS;
    $db = DB_NAME;

    $db_con = new mysqli($host, $user, $pass, $db);