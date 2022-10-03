<?php
    include('helpers/validation.php');
    include('helpers/exist.php');
    include('helpers/status.php');
    include('helpers/tokenizer.php');
    include('logs/logging.php');

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
        
        if (isset($data['frmdet-reg'])) {
            // client register
            if(!empty($data['fn']) && !empty($data['em']) && !empty($data['un']) && !empty($data['pw'])) {
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $fullname = htmlspecialchars($data['fn']);
                $email = strtolower(htmlspecialchars($data['em']));
                $username = strtolower(htmlspecialchars($data['un']));
                $password = md5($data['pw']);
                $date = new DateTime();
                $reg_date = $date->format('Y-m-d H:i:s');
                
                if (valid_fullname($fullname) == true && valid_email($email) == true && valid_username($username) == true && valid_password($data['pw']) == true) {
                    if (username_exists($conn, $username) !== false) {
                        echo status("100");
                    } else if (email_exists($conn, $email) !== false) {
                        echo status("101");
                    } else {
                        $query = "INSERT INTO users (ip_address, last_ip, fullname, email, username, pwd, token, reg_date, last_date) VALUES ('$ip_address', '$ip_address', '$fullname', '$email', '$username', '$password', '', '$reg_date', '$reg_date');";
                        $result = mysqli_query($conn, $query);
                        if (!$result) {
                            // error
                            echo status("500");
                        } else {
                            // success
                            echo status("200");
                        }
                    }
                } else {
                    echo status("403");
                }
                mysqli_close($conn);
            } else {
                echo status("403");
            }
        } else if (isset($data['f-log'])) {
            // client logging in
            if (!empty($data['unem']) && !empty($data['pw'])) {
                $ip_address = $_SERVER['REMOTE_ADDR'];
                $login = strtolower(htmlspecialchars($data['unem']));
                $password = md5($data['pw']);
                $date = new DateTime();
                $datetime = $date->format('Y-m-d H:i:s');
                
                if (valid_email($login) == true) {
                    // client logging in with email
                    $token = build_access_token($login);
                    
                    $query = "SELECT * FROM users WHERE email='$login' AND pwd='$password';";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        echo status("500");
                    } else {
                        if (mysqli_num_rows($result) >= 1) {
                            log_last_login_by_email($conn, $ip_address, $login, $datetime);
                            save_token_by_email($conn, $login, $token);
                            echo auth_token("200", $token);
                        } else {
                           echo status("403");
                        }
                    }
                } else if (valid_username($login) == true) {
                    // client logging in with username
                    $token = build_access_token($login);
                    
                    $query = "SELECT * FROM users WHERE username='$login' AND pwd='$password';";
                    $result = mysqli_query($conn, $query);
                    if (!$result) {
                        echo status("500");
                    } else {
                        if (mysqli_num_rows($result) >= 1) {
                            log_last_login_by_username($conn, $ip_address, $login, $datetime);
                            save_token_by_username($conn, $login, $token);
                            echo auth_token("200", $token);
                        } else {
                           echo status("403");
                        }
                    }
                } else {
                    echo status("403");
                }
                mysqli_close($conn);
            } else {
                // forbidden
                echo status("403");
            }
        } else if (isset($data['f-avtr'])) {
            // client uploading/updating profile picture
        } else if (isset($data['f-usr'])) {
            // client updating username
        } else if (isset($data['f-fnam'])) {
            // client updating fullname
        } else {
            echo status("403");
        }
    }
?>