<?php
    /*
        Guidelines on character count:
            3 < Fullname < 48
            5 < Email < 30
            3 < Username < 24
            6 < Password < 30
            4 < IP < 15
            
        Guidelines on valid chars:
            Fullname: Latin letters
            Email: Valid Email address
            Username: Lower case latin letters and/or numbers
            Password: -
            IP: Numbers and periods
    */
    
    // Fullname regex: /^([A-Za-z ,.-]+$)/*/
    function valid_fullname($fullname) {
        if (strlen($fullname) >= 3 && strlen($fullname) <= 48) {
            return true;
        } else {
            return false;
        }
    }
    function checkEmail($email) {
       $find1 = strpos($email, '@');
       $find2 = strpos($email, '.');
       return ($find1 !== false && $find2 !== false && $find2 > $find1);
    }
    function valid_email($email) {
        if (strlen($email) >= 5 && strlen($email) <= 30) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL) == TRUE) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    function valid_username($username) {
        if (preg_match("/^[a-z0-9_](?!.*?\.{2})[a-z0-9_.]{1,22}[a-z0-9_]$/", $username == 1)) {
           return false;
        } else {
            if (strlen($username) >= 3 && strlen($username) <= 24) {
                return true;
            } else {
                return false;
            }
        }
    }
    function valid_password($password) {
        if (strlen($password) >= 6 && strlen($password) <= 30) {
            return true;
        } else {
            return false;
        }
    }
    // IP Regex: /^((25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/
    function valid_ip($ip) {
        if (strlen($ip) >= 4 && strlen($ip) <= 15) {
            return true;
        } else {
            return false;
        }
    }
?>