<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}



$lastLikeID = $_SESSION['last_like_id'];


// Getting likes of other users on posts without reloading

// Getting recently inserted likes which were inserted under 3 seconds
$queryResult = queryFunc("SELECT * from likes WHERE like_id > $lastLikeID");
$counter = 0;
if (isData($queryResult)) {
    while ($row = isRecord($queryResult)) {
        if ($_SESSION['user_id'] != $row['user_id']) { // Checking if you are not the one who liked the post
            $postID = $row['post_id'];

            $_SESSION['last_like_id'] = $row['like_id'];

            // Getting total count of the likes of the current post
            $queryOther = queryFunc("SELECT count(*) as count FROM likes WHERE post_id='$postID'");
            $likesResult = isRecord($queryOther);
            $likes = $likesResult['count'];

            // Inserting data into the array to pass , postID and likesCount
            $data[$counter] = array('postID' => $postID,'likes' => $likes);
            $counter += 1;
        }
    }
    // Checking if there were likes of other users in last one second
    if ($counter != 0) {
        // Simple converting the array to JSON format and passing it
        echo json_encode($data);
    } else {
        // If there were no likes inserted, then just giving a JSON response for avoiding error
        echo '{"notEmpty" : "Bilal"}';
    }
    // If no user inserted likes or like
} else {
    echo '{"notEmpty" : "Bilal"}';
}
?>