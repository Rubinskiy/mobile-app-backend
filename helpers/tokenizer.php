<?php
    /*
    Auth token:
        - username-expiry_date-SECRETCODE
        - md5(username)-base64(expiry_date)-md5(SECRETCODE)
    Refresh token:
        - username-SECRETCODE
        - md5(username)-md5(SECRETCODE)
    */
    function generate_string($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    function build_access_token($username) {
        $t = time();
        $expiry_date = $t + 3600;
        $token = md5($username)."-".base64_encode($expiry_date)."-".md5(generate_string(1));
        return $token;
    }
    function build_refresh_token($username) {
        $token = md5($username)."-".md5(generate_string(1));
        return $token;
    }
    
    function save_token_by_username($conn, $username, $acc_token) {
        $query = "UPDATE users SET token='$acc_token' WHERE username='$username';";
        $result = mysqli_query($conn, $query);
    }
    function save_token_by_email($conn, $email, $acc_token) {
        $query = "UPDATE users SET token='$acc_token' WHERE email='$email';";
        $result = mysqli_query($conn, $query);
    }
    
    function check_token($conn, $token) {
        $query = "SELECT * FROM users WHERE token='$token';";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            die(json_encode(array('status' => "500")));
        } else {
            if (mysqli_num_rows($result) >= 1) {
                return true;
            } else {
               return false;
            }
        }
    }
?>