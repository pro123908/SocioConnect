<?php 


require_once('functions.php');

$counter = 0;
$queryResult = queryFunc("SELECT * FROM comments WHERE now() - createdAt < 1");
if (isData($queryResult)) {
    while ($row = isRecord($queryResult)) {
        if ($_SESSION['user_id'] != $row['user_id']) {
            $userID = $row['user_id'];

            $queryName = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name from users WHERE user_id='$userID'");
            $name =  isRecord($queryName);


            $data[$counter] = array('postID'=>$row['post_id'],'comment'=>$row['comment'],'name'=>$name['name']);
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
