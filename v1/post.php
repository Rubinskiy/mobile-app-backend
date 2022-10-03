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
        
        // create TEXT post
        if (isset($data['f-pst'])) {
            if(!empty($data['tkn']) && !empty($data['tp']) && !empty($data['cptn'])) {
                $token = htmlspecialchars($data['tkn']);
                
                if (check_token($conn, $token) == true) {
                    // valid token
                    // gather post details, then insert to db
                    $type = htmlspecialchars($data['tp']);
                    $caption = htmlspecialchars($data['cptn']);
                    $timestamp = round(microtime(true) * 1000);
                    
                    $query = "INSERT INTO posts (user, type, image, caption, likes, timestamp) SELECT users.username, '$type', '', '$caption', '', $timestamp FROM users WHERE token = '$token';";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        // error
                        echo status("500");
                    } else {
                        // success
                        echo status("200");
                    }
                    
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