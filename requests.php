<?php include "header.php"; ?>

<div>
    <?php
        if(isset($_SESSION['user_id'])){
            if(isset($_POST['accept'])){
                acceptReq($_POST['id']);
            }
            else if(isset($_POST['ignore'])){
                ignoreReq($_POST['id']);
            }

            $reqArray = queryFunc("Select * from friend_requests where to_id = ".$_SESSION['user_id']);
            if (isData($reqArray)) { 
                while ($row = isRecord($reqArray)) {
                    $from_user = queryFunc("Select first_name, last_name,user_id from users where user_id = ".$row['from_id']);
                    $from_user = isRecord($from_user);
                    $friend_req = <<<DELIMETER
                    <p>{$from_user['first_name']}  {$from_user['last_name']} Sent You a Friend Request</p>
                    <form action ="requests.php" method="post">
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