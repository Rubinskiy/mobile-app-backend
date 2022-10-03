<?php
    function username_exists($conn, $username) {
        $query = "SELECT * FROM users WHERE username='$username';";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            // error
            die(json_encode(array('status' => "500")));
        } else {
            if (mysqli_num_rows($result) >= 1) {
                return true;
            }
            else {
               return false;
            }
        }
    }
    function email_exists($conn, $email) {
        $query = "SELECT * FROM users WHERE email='$email';";
        $result = mysqli_query($conn, $query);
        if (!$result) {
            // error
            die(json_encode(array('status' => "500")));
        } else {
            if (mysqli_num_rows($result) >= 1) {
                return true;
            }
            else {
               return false;
            }
        }
    }
?>