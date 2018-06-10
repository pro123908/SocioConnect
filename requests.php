
<?php 

require_once('functions.php');

if(!isset($_SESSION['user_id'])){
    redirection('index.php');
}
if(isset($_POST['add_friend']))
    addFriend($_POST['userId']);
else if(isset($_POST['cancel_req']))
    cancelReq($_POST['userId']);
else if(isset($_POST['respond_to_request'])) 
    redirection("requests.php");
else if(isset($_POST['remove_friend']))
    removeFriend($_POST['userId']);
    
require_once('header.php');

?>



<div class='content'>
    <?php
        // Getting all your requests from database which you have received
        $userID = $_SESSION['user_id'];

        $reqArray = queryFunc("SELECT * FROM friend_requests WHERE to_id ={$userID} AND status=0");
        $friend_req = "<div class='friend-request-area'><div class='friend-request'>";
        if (isData($reqArray)) { 
            while ($row = isRecord($reqArray)) {
                // Getting the person who sent you the request
                $from_user = queryFunc("Select first_name, last_name,user_id from users where user_id = ".$row['from_id']);
                $from_user = isRecord($from_user);
                $friend_req .= <<<DELIMETER
                <p>{$from_user['first_name']}  {$from_user['last_name']} Sent You a Friend Request</p>
                <form action ="acceptRequest.php" method="post">
                    <input type="submit" name="accept" value="Confirm"> <input type="submit" name="ignore" value="Ignore">
                    <input type = "hidden" name = "id" value="{$from_user['user_id']}">
                </form></div>
DELIMETER;
                      
            }    
        }
        else{
            $friend_req .= "<p>No Friend Requests</p></div>";
        }
        // Displaying friends
        $friend_req .= '<div class="friends-list"><h1>Friends</h1><div class="friends-list-elements">';
        echo $friend_req;  
        displayFriends();
        echo "</div></div></div>";
        
    ?>
    <div class='people-you-may-know-area'>People you may know</div>
</div>

<script src="script.js"></script>