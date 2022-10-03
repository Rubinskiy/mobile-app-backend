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
    
    // add blank ppic if user has no image
    function fill_ppic($img) {
        if (!is_null($img) || $img != "") {
            return $img;
        } else {
            return "https://cdn.pixabay.com/photo/2015/10/05/22/37/blank-profile-picture-973460_960_720.png";
        }
    }
    
    // check database connection
    if (mysqli_connect_errno()) {
        // error connecting to database
        die(json_encode(array('status' => "500")));
    } else {
        // connection established
        // check for request types according to parameters
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        
        if (isset($data['f-ed'])) {
            // like post
            if(isset($data['ofs']) && !empty($data['tkn'])) {
                $offset = htmlspecialchars($data['ofs']);
                $token = htmlspecialchars($data['tkn']);
                
                if (check_token($conn, $token) == true) {
                    // valid token
                    // gather posts order by time, then structure
                    
                    $query = "";
                    if ($offset != 0) {
                        $query = "SELECT * from posts ORDER BY id DESC LIMIT 10 OFFSET $offset;";
                    } else {
                        $query = "SELECT * from posts ORDER BY id DESC LIMIT 10;";
                    }
                    $result = mysqli_query($conn, $query);
                    
                    if (!$result) {
                        die(json_encode(array('status' => "500")));
                    } else {
                        if (mysqli_num_rows($result) >= 1) {
                            $emparray = array();
                            while($row = mysqli_fetch_assoc($result))
                            {
                                $emparray[] = $row;
                            }
                            
                            $json_feed_array = array();
                            for ($x = 0; $x <= 9; $x++) {
                                $id = intval($emparray[$x]['id']);
                                
                                $user = $emparray[$x]['user'];
                                $user_query = "SELECT ppic, is_verified, is_moderator FROM users WHERE username = '$user';";
                                $user_result = mysqli_query($conn, $user_query);
                                $row_posted_user = mysqli_fetch_row($user_result);
                                $postedUserPPIC = $row_posted_user[0];
                            
                                // is user verified
                                $postedUserVerified = "";
                                $row_posted_user[1] != "0" ? $postedUserVerified = true : $postedUserVerified = false;
                                
                                // is user moderator
                                $postedUserModerator = "";
                                $row_posted_user[2] != "0" ? $postedUserModerator = true : $postedUserModerator = false;
                                
                                $type = $emparray[$x]['type'];
                                
                                $img = $emparray[$x]['image'];
                                $image = fill_ppic($img);
                                
                                $caption = $emparray[$x]['caption'];
                                $likes = $emparray[$x]['likes'];
                                $time = intval($emparray[$x]['timestamp']);
                                
                                // get current user id, and check if he has liked the post
                                $cur_user_query = "SELECT id FROM users WHERE token = '$token';";
                                $cur_user_result = mysqli_query($conn, $cur_user_query);
                                $row_cur_user = mysqli_fetch_row($cur_user_result);
                                $curUserID = $row_cur_user[0];
                                
                                $likeArray = 0;
                                $liked_determine = false;
                                if ($likes != "") {
                                    $likeArray = explode(';', $likes);
                                    
                                    if (in_array(strval($curUserID), $likeArray)) {
                                        // is in array, user liked the post
                                        $liked_determine = true;
                                    }
                                    else {
                                        // is not in array, user didnt like the post
                                        $liked_determine = false;
                                    }
                                } else {
                                    $likeArray = 0;
                                }
                                
                                $struct = array (
                                    'id' => $id,
                                    'type' => $type,
                                    'user' => array (
                                        'username' => $user,
                                        'ppic' => $postedUserPPIC,
                                        'is_verified' => $postedUserVerified,
                                        'is_mod' => $postedUserModerator
                                    ),
                                    'timestamp' => $time,
                                    'image' => $image,
                                    'caption' => $caption,
                                    'liked' => $liked_determine,
                                    'likes' => $likeArray != 0 ? count($likeArray) : $likeArray
                                );
                                $json_feed_array[] = $struct;
                            }
                            $feedAndStatus = array(
                                'status' => '200',
                                'posts' => $json_feed_array
                            );
                            echo json_encode($feedAndStatus);
                        } else {
                            die(json_encode(array('status' => "500")));
                        }
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