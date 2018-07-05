<?php 
  
  require_once('functions.php');

  if(!isset($_SESSION['user_id'])){
    redirection('index.php');
  }
  

  // Storing message to the database
  if (isset($_POST['partner'])) {
      sendMessage($_POST['partner'], $_POST['messageBody']);
      echo $_POST['partner'];
  }

  // For message Refresh
  if (isset($_GET['id'])) {
      $userID = $_SESSION['user_id'];
      $partnerID = $_GET['id'];
      $last_msg_id = $_SESSION['last_msg_id'];

     $profilePicYou = getUserProfilePic($partnerID);

    //   $profilePicQueryYou = queryFunc("SELECT profile_pic from users where user_id='$partnerID'");
    //   $profilePicQueryYouResult = isRecord($profilePicQueryYou);
    //   $profilePicYou = $profilePicQueryYouResult['profile_pic'];

      $queryResult = queryFunc("SELECT id,user_to,user_from,body from messages WHERE id>'$last_msg_id' AND user_to='$userID' AND user_from='$partnerID' AND deleted = 0");
      $counter = 0;

      if (isData($queryResult)) {
          while ($row = isRecord($queryResult)) {
              $messageBody = $row['body'];

              // Inserting data into the array to pass , postID and likesCount
              $data[$counter] = array('message' => $messageBody,'partnerID' =>$partnerID,'pic' => $profilePicYou);
              $counter += 1;
              $_SESSION['last_msg_id'] = $row['id'];
          }
    
                echo json_encode($data);

                
      } else {
          echo '{"notEmpty" : "Bilal"}';
      }
  }

?>