<?php require_once('header.php'); ?>

<div>
    <?php
        if(isset($_SESSION['user_id'])){
            
            // Getting all your requests from database which you have received
            $reqArray = queryFunc("Select * from friend_requests where to_id = ".$_SESSION['user_id']);
            if (isData($reqArray)) { 
                while ($row = isRecord($reqArray)) {
                    // Getting the person who sent you the request
                    $from_user = queryFunc("Select first_name, last_name,user_id from users where user_id = ".$row['from_id']);
                    $from_user = isRecord($from_user);
                    $friend_req = <<<DELIMETER
                    <p>{$from_user['first_name']}  {$from_user['last_name']} Sent You a Friend Request</p>
                    <form action ="acceptRequest.php" method="post">
                        <input type="submit" name="accept" value="Confirm"> <input type="submit" name="ignore" value="Ignore">
                        <input type = "hidden" name = "id" value="{$from_user['user_id']}">
                    </form>
DELIMETER;
echo $friend_req;                        
                }    
            }
            else{
                echo "You have no Friend Requests";
            }
        }
    ?>
</div>
<script src="script.js"></script>