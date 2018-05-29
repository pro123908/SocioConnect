<?php 

  require_once('functions.php');

  // For loading notifcaitons without reloading the page

  $counter = 0;

  //Selecting recent notifications which were inserted under 2 seconds
  $queryResult = queryFunc("SELECT * from notifications WHERE now() - createdAt < 2");

  if (isData($queryResult)) {
      while ($row = isRecord($queryResult)) {
          // Selecing notificaiton based on
          // If is your notification and it is not already been seen
          if ($row['d_user_id'] == $_SESSION['user_id'] && $row['seen'] != 1) {
             $person = $row['s_user_id']; // Person who generated the notification
             $post = $row['post_id']; // Post ID
             $type = $row['typeC']; // type of the notification
             $notiID = $row['noti_id']; // Notification ID

        // Selecting name of the person who geneerated the notification
              $personQuery = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$person'");
              $sPerson = isRecord($personQuery);
              $sPersonName = $sPerson['name']; // Name of that person

              // Inserting notifications into the array
              // PostID,type,notification ID and name of the source person
              $data[$counter] = array('postID' => $post,'type' => $type,'notiID' => $notiID,'name' => $sPersonName);

              // Moving on to the next notificaiton if any
              $counter += 1;
          }
      }
      // if there were no notifations for you
      if ($counter != 0) {
          // Simple converting the array to JSON format and passing it
          echo json_encode($data);
      } else {
          // If there were no notifications, then just giving a JSON response for avoiding error
          echo '{"notEmpty" : "Bilal"}';
      }
  } else {
      echo '{"notEmpty" : "Bilal"}';
  }
