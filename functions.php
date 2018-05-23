<?php
require('db.php');
session_start();


function setMessage($msg)
{
    // Setting message variable for messages

    if (!empty($msg)) {
        $_SESSION['message'] = $msg;
    } else {
        $msg = "";
    }
}

function displayMessage()
{
    // Just displaying those set messages

    if (isset($_SESSION['message'])) {
        echo $_SESSION['message'];
        unset($_SESSION['message']);
    }
}

function queryFunc($query)
{
    // Function to query the database for queries

    global $connection;
    $queryResult = mysqli_query($connection, $query);

    // If query fails
    if (!$queryResult) {
        die('Error in querying database '.mysqli_error($connection));
    }
    // Returning the results of a query
    return $queryResult;
}

function isData($queryResult)
{
    // Checking if query has returned some data or not?
    // Are there any records returned or not?
    return (mysqli_num_rows($queryResult) != 0);
}


//faster
function isRecord($queryResult)
{
    // Iterating over records one by one and returning that record
    return  (mysqli_fetch_assoc($queryResult));
}


//Slower
function fetchArray($result)
{
    return mysqli_fetch_array($result);
}

//Not working
function escapeString($string)
{
    global $connection;
    return mysqli_real_escape_string($connection, $string);
}

function hashString($string)
{
    //Function for password hashing

    // hash and salt
    $hash = '$2y$10$';
    $salt = 'thisisthestringusedfor';

    $hashed = $hash.$salt;

    // Creating hash of the passed string
    $string = crypt($string, $hashed);

    // Returning hashed string
    return $string;
}

function redirection($path)
{
    // Redirecting user to the specified path
    header('Location: '.$path);
}

function addPost($flag, $visitorID)
{
    // Adding post form
    $userID = $_SESSION['user_id'];
    if ($flag || $visitorID == $userID) {
        // 2nd condition - if you came to your profile by searching
        $addPost = "<div id='addPost' class='show'>";
    } else {
        $addPost = "<div id='addPost' class='hidden'>";
    }
    $addPost .= <<<DELIMETER
            <h2>Add a post</h2>
            <form action="post.php" method='POST'>
                <textarea name="post" id="" cols="50" rows="10" placeholder='Start Writing'></textarea><br><br>
                <input type="file"><br><br>
                <a class='postBtn' href="javascript:addPost({$userID})" >Post</a>
            </form>
        </div>
DELIMETER;
    echo $addPost;
}

function newPost($postContent)
{

    // Function for adding a post

    global $connection;

    $post = mysqli_real_escape_string($connection, $postContent);
    $userID = $_SESSION['user_id'];

    // Inserting post data
    $queryResult =  queryFunc("INSERT INTO posts(post,user_id,createdAt) VALUES('$post','$userID',now())");

    // Calling show posts method with flag 4
    showPosts('d');
}


function deletePost($postID)
{
    // Deleting post selected by passed postID
    $deleteQuery = queryFunc("DELETE from posts WHERE post_id ='$postID'");

    // Deleting comments of that post too
    $deletePostComments = queryFunc("DELETE from comments WHERE post_id ='$postID'");

    //Deleting likes of that post
    $deletePostLikes = queryFunc("DELETE from likes WHERE post_id ='$postID'");

    // Returning success message
    return $deleteQuery;
}

function deleteComment($commentID)
{
    // Deleting particular comment identified by passed comment ID
    $deleteQuery = queryFunc("DELETE from comments WHERE comment_id ='$commentID'");
    return $deleteQuery;
}

function addComment($userID, $postID, $comment)
{
    // Adding comment

    global $connection;
    //Inserting the comment using different method
    $queryInsert = $connection->prepare("INSERT INTO comments (user_id, post_id, comment,createdAt) VALUES (?, ?, ?,now())");
    $queryInsert->bind_param("iis", $userID, $postID, $comment);
    $queryInsert->execute();
    $queryInsert->close();

    
    //Generating Notification
    $whosePostQuery = queryFunc("SELECT user_id from posts where post_id='$postID'");
    $whosePost = isRecord($whosePostQuery);

    // Calling notification method for notification entry to database
    notification($userID, $whosePost['user_id'], $postID, 'commented');
    
    // Query for getting the latest comment
    $queryResult = queryFunc("SELECT comment_id from comments ORDER BY comment_id DESC LIMIT 1");
    $row = isRecord($queryResult);

    // Returning the latest comemnt ID
    return $row['comment_id'];
}

function showPosts($flag)
{
    //Selecting all the posts in a manner where user_id matches post_id
    // Querying database depending on flag value
    /*
    a => Newsfeed
    b => User Timeline
    c => Notification
    d => New post is added
    any numner => Searched user's ID;
    */
    if ($flag=='a') {
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id order by post_id desc");
    } elseif ($flag == 'b') {
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = {$_SESSION['user_id']} order by post_id desc");
    } elseif ($flag=='c') {
        $postID = $_SESSION['notiPostID'];
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE post_id='$postID'");
    } elseif ($flag == 'd') {
        $userID = $_SESSION["user_id"];
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE posts.user_id='$userID' order by post_id desc LIMIT 1");
    } elseif ($flag > 0) {
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = '$flag' order by post_id desc");
    }

    
    if (isData($queryResult)) {
        // If database returns something
        while ($row = isRecord($queryResult)) {
            if ($row['user_id'] == $_SESSION['user_id'] || isFriend($row['user_id'])) {
                $postID = $row['post_id'];
                $user = $_SESSION['user'];
                $diffTime = differenceInTime($row['createdAt']);
                $timeToShow = timeString($diffTime);
            
                // Getting likes count for the current post
                $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
                $likes = isRecord($likesResult);

                // Enabling delete option for post if it is current user's post else disabling
                if ($row['user_id'] == $_SESSION['user_id']) {
                    $PostDeleteButton = <<<PosDel
                <a  class='deleteBtn' href="javascript:deletePost({$postID})" >Delete</a>
PosDel;
                } else {
                    $PostDeleteButton = '';
                }
            
                // Rendering Post
                $post = <<<POST
            <div class='post post_{$postID}'>
                <span class='user'>{$row['name']}</span>
                <span class='postTime'>$timeToShow</span>
                <p class='postContent'>{$row['post']}</p>
                <span onmouseout='javascript:hideLikers({$postID})' onmouseover='javascript:likeUsers({$postID})' class='likeCount likeCount-{$postID}'>{$likes['count']}</span>
                <span class='likeCount likeUsers-{$postID}'></span>
                <a class='likeBtn' href='javascript:like({$postID})'>Like</a>
                <a  class='commentBtn' href="javascript:showCommentField({$postID})">Comment</a>
                {$PostDeleteButton}
            
POST;
                // Opening comment section if it is a comment notification else not
                if ($flag == 'c' && $_SESSION['notiType'] != 'liked') {
                    $commentShow = 'show';
                } else {
                    $commentShow = 'hidden';
                }

                // Comment Section of a post
                $post .= <<<POST
            <div id="post_id_{$postID}" class='{$commentShow}'>
                <div class='commentArea_{$postID}'>

POST;

                // Querying database for the current post comments if any
                $commentResult = queryFunc("SELECT comments.user_id,comment_id,comment,CONCAT(first_name,' ',last_name) as 'name',createdAt from comments inner join users on users.user_id = comments.user_id where comments.post_id ='$postID' order by createdAt");

                while ($comments = isRecord($commentResult)) {
                    $diffTime = differenceInTime($comments['createdAt']);
                    $timeToShow = timeString($diffTime);
                    $commentID = $comments['comment_id'];

                    // Enabling delete option for comment if it is current user's comment else disabling
                    if ($comments['user_id'] == $_SESSION['user_id']) {
                        $commentDeleteButton = <<<ComDel
                    <a class='commentDelete' href='javascript:deleteComment({$commentID})'>X</a>
ComDel;
                    } else {
                        $commentDeleteButton = '';
                    }

                    // Rendering comment
                    $post .= <<<POST
                <div class='comment comment_{$commentID}'>
                    {$commentDeleteButton}
                    <span class='commentUser'>{$comments['name']} : </span>
                    <span class='commentText'>{$comments['comment']}</span>
                    <span class='commentTime'>$timeToShow</span>
                </div>
            
POST;
                }
                // Rendering input field for adding comment
                $post .= <<<POST
            </div>
            <div class='commentForm'>
                <form onsubmit="return comment({$postID})" method="post" id='commentForm'>
                    <input name = "comment_{$postID}" type='text'>
                    <input type="text" value="{$postID}" style="display:none" name="post_id_{$postID}">
                    <input type="text" value="{$user}" style="display:none" name="post_user">
                    <input type='submit' id="{$postID}" value="Comment"> 
                </form>
            </div>
       
    </div>
   </div>
   <br>
POST;
    
                // Finally rendering all the content in the variable xD
                echo $post;
            }
        }
    }
}



function logout()
{
    //Closing the session and destroying all the session variables
    session_start();
    session_destroy();
    // Redirecting to the login page
    redirection('index.php');
}

function differenceInTime($createdAt)
{
    // Calculating difference in current time and time of the particular content
    $currentTime = queryFunc("SELECT TIMESTAMPDIFF(SECOND, '".$createdAt."', now()) as 'time' ");
    $currentTime = isRecord($currentTime);
    return $currentTime['time'];
}

function timeString($time)
{
    // Making time String to display time in good manner xD

    // Time in seconds
    if ($time < 60) {
        // if it is just one second
        if ($time == 1) {
            return $time ." Second Ago";
        } else {
            return $time ." Seconds Ago";
        }
    }
    // Time in minutes
    elseif ($time > 59 && $time < 3600) {
        // if it is just one minute
        if (($time / 60) < 2) {
            return floor($time / 60) . " Minute Ago";
        } else {
            return floor($time / 60) . " Minutes Ago";
        }
    }
    // Time in hours
    elseif ($time > 3599 && $time < 86400) {
        // Shouldn't it be 3600?
        // if it is just one hour
        if (($time / 3600) < 2) {
            return floor($time / 3600) . " Hour Ago";
        } else {
            return floor($time / 3600) . " Hours Ago";
        }
    }
    // Time in days
    elseif ($time > 86399) {
        // if it is just one day
        if (($time / 86400) < 2) {
            return floor($time / 86400) . " Day Ago";
        } else {
            return floor($time / 86400) . " Days Ago";
        }
    }
}

function formValidation($email, $pass, $re_pass)
{
    // Querying database to check if user exists already?
    $queryResult = queryFunc("SELECT user_id from users where email='$email'");
    $row = isRecord($queryResult);

    // Validating for against different criterias
    /*
    => If passwords are not matched
    => If email already exists
    => If password doesn't contain any of 0-9 digits
    => If password doesn't containe any of A-Z or a-z alphabets
    */
    if ($pass != $re_pass || $row['user_id'] > 0 || preg_match("/[0-9]+/", $pass) == 0 || preg_match("/[A-Za-z]+/", $pass) == 0) {
        if ($row['user_id'] > 0) {
            $_SESSION['s_email_error'] = "Email Already in Use";
        } else {
            $_SESSION['s_email_error'] = "";
        }
        if ($pass != $re_pass) {
            $_SESSION['s_pass_error'] = "Passwords Don't Match";
        } elseif (preg_match("/[0-9]+/", $pass) == 0 ||  preg_match("/[A-Za-z]+/", $pass) == 0) {
            $_SESSION['s_pass_error'] = "Password Must Contain Alphanumeric Characters";
        } else {
            $_SESSION['s_pass_error'] = "";
        }
        // If validation is unsuccessful
        return false;
    } else {
        // If validation is successful
        return true;
    }
}


function personalInfo($flag, $id)
{
    // Displaying user personal info

    // Querying database for user info
    if ($id > 0) { // You searched someone
        $queryResult = queryFunc("SELECT * from users where user_id='$id'");
    } else { // you came on your profile xD
        $queryResult = queryFunc("SELECT * from users where user_id={$_SESSION['user_id']}");
    }
    $row = isRecord($queryResult);
    $pic = $row['profile_pic'];
    // If profilePic has been uploaded then hide the form else show it
    if (isset($pic)) {
        $picForm = 'hidden';
    } else {
        $picForm = 'show';
    }

    // Rendering Personal Info Block
    $info = <<<DELIMETER
    <div id="modal" class="modal">
        <span class="close" id="modal-close" onclick="onClosedImagModal()">&times;</span>
        <img class="modal-content" id="modal-img" src='http://localhost/SocioConnect/{$pic}'>
    </div>
     <img class='dp' src='http://localhost/SocioConnect/{$pic}'alt='hello' onclick='showImage()'>
DELIMETER;
    if ($flag || ($_SESSION['user_id'] == $row['user_id'])) {
        // 2nd condtion - have you searched yourself and then came to your profile xD
        // User will have changing pic capability
        $info .= <<<DELIMETER
    <button onclick="javascript:changePic()">Change Profile Pic</button>
DELIMETER;
    }
    // You have came to someone else profile, how come you are supposed to change profile pic? xD
    $info .= <<<DELIMETER
     <p>First Name: {$row['first_name']} </p>
     <p>Last Name: {$row['last_name']}</p>
     <p>Email: {$row['email']}</p>
     <p>Age: {$row['age']}</p>
     <p>Gender: {$row['gender']}</p>
DELIMETER;

    // Profile pic input form
    if ($flag || ($_SESSION['user_id'] == $row['user_id'])) {
        // 2nd condtion - have you searched yourself and then came to your profile xD

        $info .= <<<DELIMETER
     <form action="uploadpic.php" method="post" enctype="multipart/form-data" class='formPic {$picForm}'>
        <label for='file'>Select a pic</label>
        <input type="file" name="file" style='margin-left:110px;'><br>
        <input type="submit" name="submit" value="Upload Photo">
     </form>
DELIMETER;
    }
    // Rendering all above stuff xD
    echo $info;

    // Just unsetting dp set text after printing on screen
    if (isset($_SESSION['dp_upload_message'])) {
        echo $_SESSION['dp_upload_message'];
        unset($_SESSION['dp_upload_message']);
    }
}

function notification($sUser, $dUser, $post, $type)
{
    //Checking if notification already been there
    $notiAlready = queryFunc("SELECT * from notifications WHERE s_user_id='$sUser' AND post_id='$post' AND typeC='$type'");

    if (!isData($notiAlready)) {
        //Checking if the src and dest user are not same
        if ($sUser != $dUser) {
            $notiQuery = queryFunc("INSERT INTO notifications(s_user_id,d_user_id,post_id,typeC,createdAt) VALUES('$sUser', '$dUser','$post','$type',now())");
        } else {
        }
    }
}

function showNotifications()
{
    $user = $_SESSION['user_id'];

    // Selecting notifications for the current User
    $notiQuery = queryFunc("SELECT * from notifications WHERE d_user_id='$user'");

    // flag for user realization
    $isAny = false;

    if (isData($notiQuery)) {
        // If there are notifications
        while ($row = isRecord($notiQuery)) {
            /*
               Checking if are you the one who generated the notification
               if yes then not printing the notification else printing it
            */
            
            if ($user != $row['s_user_id'] && $row['seen'] != 1) {
                $isAny = true;
                $person = $row['s_user_id'];
                $post = $row['post_id'];
                $type = $row['typeC'];
                $notiID = $row['noti_id'];

                // Selecting name of the user who generated the notification
                $personQuery = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$person'");
                $sPerson = isRecord($personQuery);
    
                // Rendering notification
                $noti = <<<NOTI
                <a href='notification.php?postID={$post}&type={$type}&notiID={$notiID}'>{$sPerson['name']} has {$type} your post<br><br></a>
NOTI;
                // You know the drill xD
                echo $noti;
            }
        }
        // Deleting notifications after they have been checked
        if ($isAny) {
            // queryFunc("DELETE from notifications WHERE d_user_id='$user'");
        } else {
            // Flag will be false, if no notifications were there to show
           // echo '<p>No new notifications</p>';
        }
    } else {
        // echo '<p>No new notifications</p>';
    }
}

//Friend Functions

function isFriend($id)
{
    // Checking if specified user your friend or not?
    $userLoggedIn = $_SESSION['user_id'];
    $friend = queryFunc("SELECT friends_array  FROM users WHERE user_id='$userLoggedIn,'");
    $friend = isRecord($friend);

    // Extracting the user if there
    if (strstr($friend['friends_array'], $id.",")) {
        return true;
    } else {
        return false;
    }
}

function reqSent($id)
{
    // Checking if request is already sent?
    $request = queryFunc("SELECT id from friend_requests where to_id ='".$id."' and from_id='" . $_SESSION['user_id'] ."'");
    if (isRecord($request)) {
        return true;
    } else {
        return false;
    }
}

function reqRecieved($id)
{
    // Checking if you have received the request from that person?
    $request = queryFunc("SELECT id from friend_requests where to_id ='".$_SESSION['user_id']."' and from_id='". $id ."'");
    if (isRecord($request)) {
        return true;
    } else {
        return false;
    }
}

function showFriendButton($id)
{
    // Showing add friend button if you came to someones else profile xD
    if ($id > 0 && $id != $_SESSION['user_id']) {
        // 2nd condition - You have not come to your profile through searching

        $button = "<form action = 'timeline.php?visitingUserID=$id' method='POST'>";
        if (isFriend($id)) {
            // If person is already your friend
            $button .= "<input type = 'Submit' name='remove_friend' value='Remove Friend'>";
        } elseif (reqSent($id)) {
            // If person hasn't accepted your request :(
            $button .= "<input type = 'Submit' name='cancel_req' value='Friend Request Sent'>";
        } elseif (reqRecieved($id)) {
            //  If you haven't responded to the person's friend request xD
            $button .= "<input type = 'Submit' name='respond_to_request' value='Respond to Friend Request'>";
        } else {
            // And if all above conditions fails, then people have no connection xD
            $button .= "<input type = 'Submit' name='add_friend' value='Add Friend'>";
        }
        $button .= "</form>";
        echo $button;
    }
}


//friend operations
function addFriend($id)
{
    // When add friend button is clicked
    // $id -> the person you are sending request too xD
    $friend = queryFunc("INSERT INTO friend_requests (to_id, from_id) values(".$id.",".$_SESSION['user_id'].")");
    redirection("timeline.php?visitingUserID=".$id);
}

function cancelReq($id)
{
    // When cancel request button is clicked
    // Deleting friend request from database
    $friend = queryFunc("DELETE FROM friend_requests WHERE to_id =".$id." AND from_id =".$_SESSION['user_id']);
    redirection("timeline.php?visitingUserID=".$id);
}

function removeFriend($id)
{
    // When remove friend button is clicked
    // Removing friends and from both's records
    updateFriendList($id, $_SESSION['user_id']);
    updateFriendList($_SESSION['user_id'], $id);
    redirection("timeline.php?visitingUserID=".$id);
}

function updateFriendList($user, $friend)
{
    $friendArray = queryFunc("select friends_array from users where user_id =".$user);
    $friendArray = isRecord($friendArray);
    $friendArray = $friendArray['friends_array'];
    // 1st -> search for this
    // 2nd -> replace with this
    // 3rd -> search in this
    $newFriendsArray = str_replace($friend.",", "", $friendArray);
    
    queryFunc("update users set friends_array ='". $newFriendsArray ."' where user_id =".$user);
}

function acceptReq($id)
{
    // When you have accepted the friend request
    // $id of the user of whom you are accepting the request
    // Updating both users records
    $friend = queryFunc("update users set friends_array = concat (friends_array,".$id ." ,',') where user_id = ".$_SESSION['user_id']);
    $friend = queryFunc("update users set friends_array = concat (friends_array,".$_SESSION['user_id'] ." ,',') where user_id = ".$id);

    // Deleting request from the person record  who sent you the request,bcoz it is accepted
    ignoreReq($id);
}

function ignoreReq($id)
{
    // Just simply deleting the request from sending user's record
    $friend = queryFunc("delete from friend_requests where to_id =".$_SESSION['user_id']." and from_id = ".$id);
}


   