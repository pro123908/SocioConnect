<?php 

  require_once('functions.php');

  $counter = 0;
  $queryResult = queryFunc("SELECT * from notifications WHERE now() - createdAt < 1");

  if(isData($queryResult)){
    while($row = isRecord($queryResult)){
      if($row['d_user_id'] == $_SESSION['user_id'] && $row['seen'] != 1){

        $person = $row['s_user_id'];
        $post = $row['post_id'];
        $type = $row['typeC'];
        $notiID = $row['noti_id'];

        $personQuery = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$person'");
        $sPerson = isRecord($personQuery);
        $sPersonName = $sPerson['name'];

        $data[$counter] = array('postID' => $post,'type' => $type,'notiID' => $notiID,'name' => $sPersonName);
        $counter += 1;


      }
    }

    echo json_encode($data);
  }
  else{
    echo '{"notEmpty" : "Bilal"}';
  }

?>