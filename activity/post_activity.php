<?php
    function like_post($conn, $token, $postID) {
        $check_like_query = "SELECT users.id, posts.likes FROM users, posts WHERE users.token='$token' AND posts.id=$postID;";
        $check_like_result = mysqli_query($conn, $check_like_query);
        if (!$check_like_result) {
            die(json_encode(array('status' => "500")));
        } else {
            if (mysqli_num_rows($check_like_result) >= 1) {
                $row = mysqli_fetch_row($check_like_result);
                $userID = $row[0];
                $curLikes = $row[1];
                $like_array = explode(';', $curLikes);
                
                if (in_array(strval($userID), $like_array)) {
                    // is in array
                }
                else {
                    // is not in array
                    if ($curLikes != "") {
                        $query = "UPDATE posts SET posts.likes = CONCAT(posts.likes, CONCAT(';', '$userID')) WHERE posts.id = $postID;";
                        $result = mysqli_query($conn, $query);
                        die(json_encode(array('status' => "200")));
                    } else {
                        $query = "UPDATE posts SET posts.likes = '$userID' WHERE posts.id = $postID;";
                        $result = mysqli_query($conn, $query);
                        die(json_encode(array('status' => "200")));
                    }
                    break;
                }
            } else {
                die(json_encode(array('status' => "500")));
            }
        }
    }
    function unlike_post($conn, $token, $postID) {
        $check_like_query = "SELECT users.id, posts.likes FROM users, posts WHERE users.token='$token' AND posts.id=$postID;";
        $check_like_result = mysqli_query($conn, $check_like_query);
        if (!$check_like_result) {
            die(json_encode(array('status' => "500")));
        } else {
            if (mysqli_num_rows($check_like_result) >= 1) {
                $row = mysqli_fetch_row($check_like_result);
                $userID = $row[0];
                $curLikes = $row[1];
                $like_array = explode(';', $curLikes);
                
                if (in_array(strval($userID), $like_array)) {
                    // is in array
                    $newLikesArray = array_diff($like_array, array($userID));
                    $newLikes = join(";", $newLikesArray);
                    $query = "UPDATE posts SET posts.likes = '$newLikes' WHERE posts.id = $postID;";
                    $result = mysqli_query($conn, $query);
                    die(json_encode(array('status' => "200")));
                    break;
                }
                else {
                    // is not in array
                }
            } else {
                die(json_encode(array('status' => "500")));
            }
        }
    }
?>