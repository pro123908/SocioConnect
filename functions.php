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
        $addPost = "<div class='show'>";
    } else {
        $addPost = "<div class='hidden'>";
    }
    $addPost .= <<<DELIMETER
            <div class='post-options'></div>
            <form action="post.php" method='POST'>
            <textarea name="post" id="" cols="30" rows="10" placeholder='Share what you are thinking here' class="post-input"></textarea>
            <br>
            <div class='post-btn-container'>
            <a  href="javascript:addPost({$userID})"  class='add-post-btn'>Post</a>
            </div>
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

    //Getting post ID of inserted POST
    $queryPostID = queryFunc("SELECT post_id from posts WHERE user_id='$userID' order by post_id desc LIMIT 1");
    $recordPostID = isRecord($queryPostID);
    $postID = $recordPostID['post_id'];

    // Generating Notification for friends
    $queryFriendsList = queryFunc("SELECT friends_array FROM users WHERE user_id='$userID'");

    $friendsList = isRecord($queryFriendsList);
    $friendsListSeparated = explode(',', $friendsList['friends_array']);

    // notification for each friend
    for ($i = 0; $i< sizeof($friendsListSeparated)-1;$i++) {
        $friend_id = $friendsListSeparated[$i];
        notification($userID,$friend_id,$postID,'post');
    }


    // Calling show posts method with flag d
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

    //Deleting notifications of that post
    queryFunc("DELETE FROM notifications WHERE post_id='$postID'");

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

    $userID = $_SESSION["user_id"];


    if ($flag=='a') {
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id order by post_id desc");
    } elseif ($flag == 'b') {
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = {$_SESSION['user_id']} order by post_id desc");
    } elseif ($flag=='c') {
        $postID = $_SESSION['notiPostID'];
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE post_id='$postID'");
    } elseif ($flag == 'd') {
        
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE posts.user_id='$userID' order by post_id desc LIMIT 1");
    } elseif ($flag > 0) {
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = '$flag' order by post_id desc");
    }
    // Profile Pic query
    $profilePicQuery = queryFunc("SELECT profile_pic from users where user_id='$userID'");
    $profilePicResult = isRecord($profilePicQuery);
    $profilePic = $profilePicResult['profile_pic'];

    
    if (isData($queryResult)) {
        // If database returns something
        while ($row = isRecord($queryResult)) {
            if ($row['user_id'] == $_SESSION['user_id'] || isFriend($row['user_id'])) {

                
                $postID = $row['post_id'];
                $userID = $_SESSION['user_id'];
                $user = $_SESSION['user'];
                $fUser = $row['user_id'];
                $diffTime = differenceInTime($row['createdAt']);
                $timeToShow = timeString($diffTime);
            
                // Getting likes count for the current post
                $likesResult = queryFunc("SELECT user_id,count(*) as count from likes where post_id='$postID'");
                $likes = isRecord($likesResult);

                // Getting number of comments for post
                $commentCountResult = queryFunc("SELECT count(*) as count from comments where post_id='$postID'");
                $commentsCount = isRecord($commentCountResult);
                

                if($likes['user_id'] == $_SESSION['user_id']){
                    $likeIcon = "<i class='blue far fa-thumbs-up'></i>";
                }
                else{
                    $likeIcon = "<i class='far fa-thumbs-up'></i>";
                }

                // Enabling delete option for post if it is current user's post else disabling
                if ($row['user_id'] == $_SESSION['user_id']) {
                    $PostDeleteButton = <<<PosDel
                    <div class='post-delete-icon'>
                    <i onclick="javascript:deletePost({$postID})" class="tooltip-container far fa-trash-alt"><span class='tooltip tooltip-right'>Remove</span></i>
                    </div>
PosDel;
                } else {
                    $PostDeleteButton = '';
                }
            
                // Rendering Post
                $post = <<<POST
            <div class='post post-{$postID}'>
                <div class='post-content'>
                {$PostDeleteButton}
                <div class='post-header'>
                <a href='timeline.php?visitingUserID={$fUser}'><img src='{$row['profile_pic']}' class='post-avatar post-avatar-40'/></a>
                
                <div class='post-info'>
                <a href='timeline.php?visitingUserID={$fUser}' class='user'>{$row['name']}</a>
                <span class='post-time'>$timeToShow</span>
                </div>
                </div>
                
                
                
                
                <p>{$row['post']}</p>
                <div class='post-stats'>
                <span onmouseout='javascript:hideLikers({$postID})' onmouseover='javascript:likeUsers({$postID})' class='tooltip-container like-count like-count-{$postID}'><i class='like-count-icon fas fa-thumbs-up'></i> {$likes['count']}
                <span class='tooltip tooltip-bottom count'></span>
                </span>
                <a href="javascript:showCommentField({$postID})" class='comment-count'><i class='fas fa-comment-dots comment-count-{$postID}'></i> {$commentsCount['count']}</a>
                </div>
                </div>
                <div class='post-buttons'>
                <a class='post-btn like-btn' href='javascript:like({$postID})'>{$likeIcon} Like</a>
                <a  class='post-btn comment-btn' href="javascript:showCommentField({$postID})"><i class="far fa-comment-dots"></i> Comment</a>
                </div>
                
            
POST;
                // Opening comment section if it is a comment notification else not
                if ($flag == 'c' && $_SESSION['notiType'] == 'commented') {
                    $commentShow = 'show';
                } else {
                    $commentShow = 'hidden';
                }

                // Comment Section of a post
                $post .= <<<POST
            <div id="comment-section-{$postID}" class='{$commentShow}'>
                <div class='comment-area-{$postID}'>

POST;

                // Querying database for the current post comments if any
                $commentResult = queryFunc("SELECT comments.user_id,comment_id,comment,CONCAT(first_name,' ',last_name) as 'name',createdAt,users.profile_pic from comments inner join users on users.user_id = comments.user_id where comments.post_id ='$postID' order by createdAt");

                while ($comments = isRecord($commentResult)) {
                    $diffTime = differenceInTime($comments['createdAt']);
                    $timeToShow = timeString($diffTime);
                    $commentID = $comments['comment_id'];

                    // Enabling delete option for comment if it is user's post else disabling
                    if ($comments['user_id'] == $_SESSION['user_id'] || $_SESSION['user_id'] == $row['user_id']) {
                        $commentDeleteButton = <<<ComDel
                    <i class='tooltip-container far fa-trash-alt comment-delete' onclick='javascript:deleteComment({$commentID})'><span class='tooltip tooltip-right'>Remove</span></i>
ComDel;
                    } else {
                        $commentDeleteButton = '';
                    }

                    // Rendering comment
                    $post .= <<<POST
                <div class='comment comment-{$commentID}'>
                
                    <div class='user-image'>
                        <a href='timeline.php?visitingUserID={$comments['user_id']}'><img src='{$comments['profile_pic']}' class='post-avatar post-avatar-30' /></a>
                    </div>
                    
                    <div class='comment-info'>
                    {$commentDeleteButton}
                    <div class='comment-body'>
                    <a href='timeline.php?visitingUserID={$comments['user_id']}' class='comment-user'>{$comments['name']} : </a>
                    <span class='comment-text'>{$comments['comment']}</span>
                    <span class='comment-time'>$timeToShow</span>
                    </div>
                    
                    </div>
                </div>
            
POST;
                }
                // Rendering input field for adding comment
                $post .= <<<POST
            </div>
            <div class='comment-form'>
                <form onsubmit="return comment({$postID})" method="post" id='commentForm'>
                    <input name = "comment_{$postID}" type='text' autocomplete = "off">
                    <input type="text" value="{$postID}" style="display:none" name="post_id_{$postID}">
                    <input type="text" value="{$user}" style="display:none" name="post_user">
                    <input type="text" value="{$profilePic}" style="display:none" name="pic_user">
                    <input style='display:none;' type='submit' id="{$postID}" value="Comment" > 
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
        }
        else if($time == 0){
            return "Just Now";
        } else {
            return $time ." Seconds Ago";
        }
    }
    // Time in minutes
    elseif ($time > 59 && $time < 3600) {
        // if it is just one minute
        if (($time / 60) < 2) {
            return floor($time / 60) . " Minute Ago";
        } 
        else {
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
    //Checking if notification already been there and is seen
    $notiAlready = queryFunc("SELECT * from notifications WHERE s_user_id='$sUser' AND post_id='$post' AND typeC='$type' AND d_user_id='$dUser' AND seen != 1");

    if (!isData($notiAlready)) {
        //Checking if the src and dest user are not same
        if ($sUser != $dUser) {
            $notiQuery = queryFunc("INSERT INTO notifications(s_user_id,d_user_id,post_id,typeC,createdAt) VALUES('$sUser', '$dUser','$post','$type',now())");
        } else {
        }
    }
}

function showNotifications($flag)
{
    $user = $_SESSION['user_id'];

    if ($flag==10) {
        // Selecting notifications for the current User
        $notiQuery = queryFunc("SELECT * from notifications WHERE d_user_id='$user' order by noti_id desc LIMIT 10");
        $postAvatar = 'post-avatar-30';
    }else{
        $notiQuery = queryFunc("SELECT * from notifications WHERE d_user_id='$user' order by noti_id desc");
        $postAvatar = 'post-avatar-40';
    }

    // flag for user realization
    $isAny = false;

    if (isData($notiQuery)) {
        // If there are notifications
        $notiCounter = 0;
        while ($row = isRecord($notiQuery)) {
            /*
               Checking if are you the one who generated the notification
               if yes then not printing the notification else printing it
            */
            
            if (true) {
                $isAny = true;
                $sUser = $row['s_user_id'];
                $postID = $row['post_id'];
                $type = $row['typeC'];
                $notiID = $row['noti_id'];
                $diffTime = differenceInTime($row['createdAt']);
                $time = timeString($diffTime);
                $colorNoti = '';

                if ($notiCounter == 0) {
                    $_SESSION['last_noti_id'] = $notiID;
                    $notiCounter = 1;
                }

                if ($row['seen'] == 0) {
                    $colorNoti = 'noSeen';
                }
        
                // Selecting name of the user who generated the notification
                $personQuery = queryFunc("SELECT profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$sUser'");
                $sPerson = isRecord($personQuery);
                
                
            if ($type=='post') {
                $conflict = 'posted';
                $notiIcon = 'far fa-user';
            } elseif($type=='commented') {
                $conflict = 'on your post';
                $notiIcon = 'far fa-comment-dots';
            }else{
                $conflict = 'your post';
                $notiIcon = 'far fa-thumbs-up';
            }

            $noti = <<<NOTI
                <a href='notification.php?postID={$postID}&type={$type}&notiID={$notiID}' class='notification  {$colorNoti}'>
                <span class='notification-image'>
                <img src='{$sPerson['profile_pic']}' class='post-avatar $postAvatar' />
                </span>
                <span class='notification-info'>
            <span class='notification-text'>{$sPerson['name']} has {$type} {$conflict}</span><i class='noti-icon {$notiIcon}'></i><span class='noti-time'>{$time}</span></span></a>
NOTI;
        echo $noti;
            }

            // $noti .= '</div>';
           
        }

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

function checkUserState($id){
    if (isFriend($id)) {
        // If person is already your friend
        $state = "<input type = 'Submit' name='remove_friend' value='Remove Friend'>";
    } elseif (reqSent($id)) {
        // If person hasn't accepted your request :(
        $state = "<input type = 'Submit' name='cancel_req' value='Friend Request Sent'>";
    } elseif (reqRecieved($id)) {
        //  If you haven't responded to the person's friend request xD
        $state = "<input type = 'Submit' name='respond_to_request' value='Respond to Friend Request'>";
    } else {
        // And if all above conditions fails, then people have no connection xD
        $state = "<input type = 'Submit' name='add_friend' value='Add Friend'>";
    }
    return $state;
}
function showFriendButton($id)
{
    // Showing add friend button if you came to someones else profile xD
    if ($id > 0 && $id != $_SESSION['user_id']) {
        // 2nd condition - You have not come to your profile through searching

        $button = "<form action = 'timeline.php?visitingUserID=$id' method='POST'>";
        $button .= checkUserState();
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

function removeFriend($id,$redirection = "")
{
    // When remove friend button is clicked
    // Removing friends and from both's records
    updateFriendList($id, $_SESSION['user_id']);
    updateFriendList($_SESSION['user_id'], $id);
    if($redirection == "")
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

function displayFriends($count)
{
    // Displaying all friends of current user

    $userID = $_SESSION['user_id'];

    $queryResult = queryFunc("SELECT friends_array from users WHERE user_id='$userID'");

    $friendsList = isRecord($queryResult);
    // Breaking the friends list in array
    $friendsListSeparated = explode(',', $friendsList['friends_array']);

    if($count == 2){
        $expression = 2;
    }
    else{
        $expression = sizeof($friendsListSeparated)-1;
    }

    for ($i = 0; $i< $expression;$i++) {
        $friend_id = $friendsListSeparated[$i]; // friend
        if($friend_id){
            // Getting name of that friend
            $queryFriends = queryFunc("SELECT *,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friend_id'");

            $friend = isRecord($queryFriends);

            $content = <<<FRIEND
                <div class='friend'>
                <div class='friend-image'>
                <img class='post-avatar post-avatar-30' src='{$friend['profile_pic']}'  >
                </div>
                
                <div class='friend-info'>
                    <a href="timeline.php?visitingUserID={$friend['user_id']}" class='friend-text'>{$friend['name']}</a>            
                </div>
                <div class='friend-action'>
                <div>
                <a href="javascript:removeFriend({$friend['user_id']})" class='remove-friend'><i class="tooltip-container fas fa-times">
                <span class='tooltip tooltip-right'>Remove Friend</span>
                </i></a>
                </div>
            </div>
            </div>
FRIEND;
            echo $content;
        }
    }
}

//Message Functions
function sendMessage($user_to,$message_body){
    $user_from = $_SESSION['user_id'];
    $flag = 0;
    global $connection;
    $queryInsert = $connection->prepare("INSERT INTO messages (user_to, user_from, body, opened, viewed, deleted, dateTime) VALUES (?,?,?,?,?,?,now())");
    $queryInsert->bind_param("iisiii", $user_to, $user_from, $message_body, $flag, $flag, $flag);
    $queryInsert->execute();
    $queryInsert->close();
}

function showMessages($partnerId){
    //Update opened to seen
    $userLoggedIn = $_SESSION['user_id'];
    $seen = queryFunc("update messages set opened = '1' where user_to = '$userLoggedIn'");

    $getConvo = queryFunc("select * from messages where (user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId')");

    while($row = isRecord($getConvo)){
        if($row['user_to'] == $userLoggedIn)
            $convo = "<div id='blue'>";
        else
            $convo = "<div id='green'>";

        $convo.= $row['body']. "</div><hr>";
         
        echo $convo;
        $_SESSION['last_msg_id'] = $row['id'];
    }
}

function getRecentChatsUserIds(){
    $recentConvos = array();
    //Getting ids of all the users where messages are received from
    $senderOfRecentMsgs = queryFunc("SELECT id,user_from,user_to FROM messages where user_to = ".$_SESSION['user_id']." or user_from = ".$_SESSION['user_id']." ORDER BY id DESC ");
    $flag = 0;
    if(isData($senderOfRecentMsgs)){
        while($row = isRecord($senderOfRecentMsgs)){
            if($flag == 0 ){
                $_SESSION['last_message_retrieved_for_recent_convos'] = $row['id'] ;
                $flag = 1;
            }
            //if user logged in is the sender then store reciever's id, else store sender's id
            $idToPush = ($row['user_from'] == $_SESSION['user_id'] ? $row['user_to'] : $row['user_from']);
            //Check whether that sender is already in the list, if not, only then push his id
            if(array_search($idToPush,$recentConvos) === false ){
                array_push($recentConvos,$idToPush);      
            }
        }
        return $recentConvos;
    }
    return false;
}

function getUserFirstAndLastName($user_id){
    $user_name = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id=".$user_id);
    $user_name = isRecord($user_name);
    return $user_name['name'];   
}

function getRecentChatsUsernames($recentConvos){
    // Getting names of users whose ids are passed
    $counter = 0;
    while($counter < sizeof($recentConvos)){
        $recentUser[$counter] = getUserFirstAndLastName($recentConvos[$counter]);
        $counter++;
    }
    return $recentUser;
}

function getPartnersLastMessage($partnerId){
    $userLoggedIn = $_SESSION['user_id'];
    $details = queryFunc("SELECT user_from,body,dateTime from messages where (user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId') order by id desc limit 1");
    $details = isRecord($details);
    return $details;
}
    
function showRecentChats(){
    $recentUserIds = getRecentChatsUserIds(); //IDS of users
    if($recentUserIds){
        $recentUsernames = getRecentChatsUsernames($recentUserIds); // Names of users
        $counter = 0;
        while($counter < sizeof($recentUsernames)){
            $lastMessageDetails = getPartnersLastMessage($recentUserIds[$counter]);
            $from = $lastMessageDetails['user_from'];
            if($from == $_SESSION['user_id'])
                $from = "You ";
            else    
                $from = getUserFirstAndLastName($from);
            $msg = $lastMessageDetails['body'];
            $at =  timeString(differenceInTime($lastMessageDetails['dateTime']));
            $user = <<<DELIMETER
            <div class='recent_user recent_user_{$recentUserIds[$counter]}'>
                <a href='messages.php?id={$recentUserIds[$counter]}'><button class="recent_username" >{$recentUsernames[$counter]}</button></a>
                <p>{$from}:{$msg}</p>
                <p>{$at}</p>
            </div>
DELIMETER;
            echo $user;  
            $counter++;
        }   
    }
}

function searchUsersFortChats(){
    $search = <<<DELIMETER
    <div class="search">
    <form action="search.php" method="get" name="message_search_form">
      <input type="text"  onkeyup="getUsers(this.value,0)" name="q" placeholder="Search..." autocomplete = "off" id="message_search_text_input">
    </form>
    <div class="search_results_for_messages"></div>
    <div class="message_search_results_footer_empty"></div>
  </div>
DELIMETER;
    echo $search;
}

function getSearchedUsers($value,$flag){
    //flag == 0 ==> called from search in messages.php 
    //flag == 1 ==> called from normal search
    //flag == 2 ==> called from allSearchResults.php
    if (strlen($value) == 0) {
        echo " ";
    } else {
        $value = strtolower($value);
        //explode breakes the string into array, each substring is made when the first arg of explode is found in the string
        $names = explode(" ", $value);
        if (count($names) == 2) {
            //if there there are two substrings then it would search for first substirng in first name and second string in the last name

            if ($flag == 2) {
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,username,user_id from users where lower(first_name) like '$names[0]%' AND lower(last_name) like '$names[1]%'");
            } else {
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,username,user_id from users where lower(first_name) like '$names[0]%' AND lower(last_name) like '$names[1]%' limit 5");
            }
        } else {
            //if there is only one substring, i.e no spaces are present in the input then it would search that substring in both first name and last name
            if ($flag == 2) {
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,username,user_id from users where lower(first_name) like '$names[0]%' OR lower(last_name) like '$names[0]%'");
            } else {
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,username,user_id from users where lower(first_name) like '$names[0]%' OR lower(last_name) like '$names[0]%' limit 5");
            }
        }
        if (isData($users)) {
            if ($flag == 1 || $flag == 2) {
                while ($row = isRecord($users)) {
                    $user = <<<DELIMETER
                <div class='search-person'>
                <div class='search-person-image'>
                <img src='{$row['profile_pic']}' class='post-avatar post-avatar-30'/>
                </div>
                <a href='timeline.php?visitingUserID={$row['user_id']}' class='search-person-info'>
                <span class='person-name'>{$row['name']}</span>
                </a>
DELIMETER;
                    if ($row['user_id'] != $_SESSION['user_id']) {
                        $user .= <<<DELIMETER
                    <div class='person-message'>
                    <a href='messages.php?id={$row['user_id']}'><i class='fas fa-envelope message-icon'></i></a>
                    </div>
                </div>
DELIMETER;
                    } else {
                        $user .= '</div>';
                    }
                    echo $user;
                }
            } else {
                while ($row = isRecord($users)) {
                    $user = <<<DELIMETER
            <div class='resultDisplay  resultDisplayForMessages'>
                <a href='messages.php?id={$row['user_id']}' style='color: #000'>
                    <div class='liveSearchProfilePic liveSearchProfilePicForMessages'>
                        <img src={$row['profile_pic']} height=38px width=38px>
                    </div>
                    <div class='liveSearchText'>
                        {$row['first_name']} {$row['last_name']}
                        <p style='margin: 0;'>{$row['username']}</p>
                    </div>
                </a>
            </div>
DELIMETER;
                    echo $user;
                }
            }
        }else{
            echo 'No';
        }
    }

}

function getRecentConvo(){
    $userLoggedIn =$_SESSION['user_id'];
    $recentUser = queryFunc("SELECT user_to,user_from from messages where user_to = ".$userLoggedIn." OR user_from = ".$userLoggedIn." order by id DESC limit 1");
    $recentUser = isRecord($recentUser);
    $recentPartnerId = ($recentUser['user_from'] == $userLoggedIn) ? $recentUser['user_to'] : $recentUser['user_from'];
    redirection("http://localhost/socioConnect/messages.php?id=".$recentPartnerId);
}


function profilePic($id){
    

    $queryResult = queryFunc("SELECT * FROM users WHERE user_id='$id'");
    $queryUser = isRecord($queryResult);
    $name = $queryUser['first_name'].' '.$queryUser['last_name'];
    

    $content =<<<PROFILE
    <div class='user-cover'>
        <div class='user-pic'>
            <span class='user-pic-container'>
            <img src='{$queryUser['profile_pic']}'  />
            <span>
        </div>
    </div>
    <div class='user-info'>
    <h3>{$name}</h3>
    <span>{$queryUser['email']}</span>
    </div>
PROFILE;
    
    echo $content;
}