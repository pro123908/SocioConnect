<?php 

require_once('functions.php');

$queryResult = queryFunc("SELECT * from likes WHERE now() - createdAt < 3");
$counter = 0;
if (isData($queryResult)) {
    while ($row = isRecord($queryResult)) {
        if ($_SESSION['user_id'] != $row['user_id']) {
            $postID = $row['post_id'];

            $queryOther = queryFunc("SELECT count(*) as count FROM likes WHERE post_id='$postID'");
            $likesResult = isRecord($queryOther);
            $likes = $likesResult['count'];

            $data[$counter] = array('postID' => $postID,'likes' => $likes);
            $counter += 1;
        }
    }

    if ($counter != 0) {
        echo json_encode($data);
    } else {
        echo '{"notEmpty" : "Bilal"}';
    }

    
} else {
    echo '{"notEmpty" : "Bilal"}';
}
