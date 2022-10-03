<?php
    function generate_filename() {
        $filename = date("m")."-".date("y").".json";
        return $filename;
    }
    function generate_foldername() {
        $foldername = date("Y");
        return $foldername;
    }
    function log_user_login($ip, $username) {
        $filename = "logs/".generate_foldername()."/".generate_filename();
        if(!file_exists(dirname($filename))) {
            mkdir(dirname($filename), 0777, true);
            file_put_contents($filename, "[{}]");
        }
        
        $date = new DateTime();
        $datetime = $date->format('Y-m-d H:i:s');
        $log_contents = array (
            'u' => $username,
            'ip' => $ip,
            't' => $datetime,
        );
        
        $handle = @fopen($filename, 'r+');
        if ($handle == null) {
            $handle = fopen($filename, 'w+');
        }
        if ($handle) {
            fseek($handle, 0, SEEK_END);
            if (ftell($handle) > 0) {
                fseek($handle, -1, SEEK_END);
                fwrite($handle, ',', 1);
                fwrite($handle, json_encode($log_contents) . ']');
            }
            else {
                fwrite($handle, json_encode($log_contents));
            }
            fclose($handle);
        }
    }
    function log_last_login_by_email($conn, $ip, $email, $last_date) {
        $query = "UPDATE users SET last_ip = '$ip', last_date = '$last_date' WHERE email='$email'";
        $result = mysqli_query($conn, $query);
    }
    function log_last_login_by_username($conn, $ip, $username, $last_date) {
        $query = "UPDATE users SET last_ip = '$ip', last_date = '$last_date' WHERE username='$username';";
        $result = mysqli_query($conn, $query);
    }
?>