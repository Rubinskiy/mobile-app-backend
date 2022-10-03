<?php
    include('helpers/validation.php');
    include('helpers/exist.php');
    include('helpers/status.php');
    include('helpers/tokenizer.php');
    include('logs/logging.php');
    include('activity/post_activity.php');

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Headers: Content-Type, Accept');
    header('Content-Type: application/json');
    
    // database connection init
    $server_name = "";
    $server_username = "";
    $server_password = "";
    $database_name = "";
    
    $conn = mysqli_connect(
        $server_name, 
        $server_username, 
        $server_password, 
        $database_name
    );
    
    // check database connection
    if (mysqli_connect_errno()) {
        // error connecting to database
        die(json_encode(array('status' => "500")));
    } else {
        // connection established
        // check for request types according to parameters
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['f-lk'])) {
            // like post
            if(!empty($data['pid']) && !empty($data['tkn'])) {
                $postID = htmlspecialchars($data['pid']);
                $token = htmlspecialchars($data['tkn']);
                
                if (check_token($conn, $token) == true) {
                    // valid token
                    like_post($conn, $token, $postID);
                } else {
                    echo status("403");
                }
                
                mysqli_close($conn);
            } else {
                echo status("403");
            }
        } else if (isset($data['f-ulk'])) {
            // unlike post
            if(!empty($data['pid']) && !empty($data['tkn'])) {
                $postID = htmlspecialchars($data['pid']);
                $token = htmlspecialchars($data['tkn']);
                
                if (check_token($conn, $token) == true) {
                    // valid token
                    unlike_post($conn, $token, $postID);
                } else {
                    echo status("403");
                }
                
                mysqli_close($conn);
            } else {
                echo status("403");
            }
        } else {
            echo status("403");
        }
    }
?>