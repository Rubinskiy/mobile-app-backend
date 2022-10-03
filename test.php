<?php
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $offset = htmlspecialchars($data['ofs']);
    $token = htmlspecialchars($data['tkn']);
    
    echo json_encode(array('data' => $data));
?>