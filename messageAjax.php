<?php 
  require_once('functions.php');

  if (isset($_POST['partner'])) {
      sendMessage($_POST['partner'], $_POST['messageBody']);
      echo $_POST['partner'];

      // To be deleted
    //   $userID = $_SESSION['user_id'];
    //   $partnerID = $_POST['partner'];

    //   $queryMessage = queryFunc("SELECT body FROM messages WHERE user_from='$userID' AND user_to='$partnerID' order by id desc limit 1");

    //   $queryMessageResult = isRecord($queryMessage);

    //   $message = $queryMessageResult['body'];

    //   $data = array('message' => $message);

    //   echo json_encode($data);
  }

  if (isset($_GET['id'])) {
      $userID = $_SESSION['user_id'];
      $partnerID = $_GET['id'];
      $last_msg_id = $_SESSION['last_msg_id'];

      $queryResult = queryFunc("SELECT id,user_to,user_from,body from messages WHERE id>'$last_msg_id' AND user_to='$userID' AND user_from='$partnerID'");
      $counter = 0;
  

      if (isData($queryResult)) {
          while ($row = isRecord($queryResult)) {
              $messageBody = $row['body'];

              // Inserting data into the array to pass , postID and likesCount
              $data[$counter] = array('message' => $messageBody,'partnerID' =>$partnerID);
              $counter += 1;
              $_SESSION['last_msg_id'] = $row['id'];
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
  }
