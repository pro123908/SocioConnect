<?php 

/* Farigh */
  require_once('functions.php');

  if(!isset($_SESSION['user_id'])){
     redirection('index.php');
  }  


  // For loading notifcaitons without reloading the page

if(isset($_GET['refresh'])){

    $counter = 0;
    $lastNotiID = $_SESSION['last_noti_id'];
    $userID = $_SESSION['user_id'];
    
    $queryResult = queryFunc("SELECT * from notifications WHERE (seen !=1 AND noti_id > $lastNotiID) AND d_user_id='$userID' order by noti_id desc");
    
    
    if (isData($queryResult)) {
        $notiCounter = 0;
            while ($row = isRecord($queryResult)) {
                // Selecing notificaiton based on
                // If is your notification and it is not already been seen
            
                $person = $row['s_user_id']; // Person who generated the notification
                $post = $row['post_id']; // Post ID
                $type = $row['typeC']; // type of the notification
                $notiID = $row['noti_id']; // Notification 
                
                if($notiCounter == 0){
                $_SESSION['last_noti_id'] = $notiID;
                $notiCounter = 1;
                }
                
                // Selecting name of the person who geneerated the notification
                $personQuery = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$person'");
                $sPerson = isRecord($personQuery);
                $sPersonName = $sPerson['name']; // Name of that person
    
                $personPic = getUserProfilePic($person);
    
    
                // Inserting notifications into the array
                // PostID,type,notification ID and name of the source person
                $data[$counter] = array('lastID' => $lastNotiID,'postID' => $post,'type' => $type,'notiID' => $notiID,'name' => $sPersonName,'profilePic' => $personPic);
    
                // Moving on to the next notificaiton if any
                $counter += 1;
            }
            // if there were no notifations for you
            // if ($counter != 0) {
            //   // Simple converting the array to JSON format and passing it
            //   echo json_encode($data);
            // } else {
            //   // If there were no notifications, then just giving a JSON response for avoiding error
            //   echo '{"notEmpty" : "Bilal"}';
            // }
    
              echo json_encode($data);
    } 
    else {
          echo '{"notEmpty" : "Bilal"}';
    }


}

?>