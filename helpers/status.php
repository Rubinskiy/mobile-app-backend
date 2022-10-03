<?php
    /*
        Status code:
            100 => Username exists
            101 => Email exists
    */
    function status($code) {
        return json_encode(array('status' => $code));
    }
    function auth_token($code, $token) {
        return json_encode(array('status' => $code, 'token' => $token));
    }
?>