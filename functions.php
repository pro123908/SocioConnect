<?php
require('db.php');
session_start();

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
        $addPost = "<div class='show'>"; //Showing add post area
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

function getRecordsFromQuery($queryResult)
{
    if (isData($queryResult)) {
        return isRecord($queryResult);
    }
}

function getTime($time)
{
    return timeString(differenceInTime($time));
}

function newPost($postContent)
{
    // Function for adding a post
    global $connection;
    $post = mysqli_real_escape_string($connection, $postContent);
    $userID = $_SESSION['user_id'];

    // Inserting post data
    $queryResult =  queryFunc("INSERT INTO posts(post,user_id) VALUES('$post','$userID')");
    // ID of top inserted post
    $ID = mysqli_insert_id($connection);

    //Getting info of inserted POST
    // $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE posts.user_id='$userID' AND post = '$post' order by post_id desc LIMIT 1");

    //Getting info of last inserted POST
    $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE post_id='$ID'");

    if ($queryResult = getRecordsFromQuery($queryResult)) {
        $postID = $queryResult['post_id'];
        $userID = $_SESSION['user_id'];
        $user = $_SESSION['user'];
        $profilePic = $queryResult['profile_pic'];
        $timeToShow = getTime($queryResult['createdAt']);
        
        $PostDeleteButton = <<<PosDel
        <div class='post-delete-icon'>
        <i onclick="javascript:deletePost({$postID})" class="tooltip-container far fa-trash-alt"><span class='tooltip tooltip-right'>Remove</span></i>
        </div>
PosDel;

        $post = <<<POST
        <div class='post post-{$postID}'>
            <div class='post-content'>
                {$PostDeleteButton}
                <div class='post-header'>
                    <a href='timeline.php?visitingUserID={$userID}'><img src='{$queryResult['profile_pic']}' class='post-avatar post-avatar-40'/></a>
        
                    <div class='post-info'>
                        <a href='timeline.php?visitingUserID={$userID}' class='user'>{$queryResult['name']}</a>
                        <span class='post-time'>$timeToShow</span>
                    </div>
                </div>
        
                <p>{$queryResult['post']}</p>
                <div class='post-stats'>
                    <span onmouseout='javascript:hideLikers({$postID})' onmouseover='javascript:likeUsers({$postID})' class='tooltip-container like-count like-count-{$postID}'><i class='like-count-icon fas fa-thumbs-up'></i> 0</span>
                    <span class='tooltip tooltip-bottom count'></span>
                    <a href="javascript:showCommentField({$postID})" class='comment-count'><i class='fas fa-comment-dots comment-count-{$postID}'></i> 0</a>
                </div>
            </div>
            <div class='post-buttons'>
                <a class='post-btn like-btn' href='javascript:like({$postID})'><i class='far fa-thumbs-up'></i> Like</a>
                <a  class='post-btn comment-btn' href="javascript:showCommentField({$postID})"><i class="far fa-comment-dots"></i> Comment</a>
            </div>    
            <div id="comment-section-{$postID}" class='hidden'>
                <div class='comment-area-{$postID}'></div>
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
        </div>
        <br>
POST;
    
        // Finally rendering all the content in the variable xD
        echo $post;
        
        $_SESSION['no_of_posts_changed']++; // ???

        //FASAD KI JAR - koi baat nahi XD
        //Generating Notification for friends
        // $queryFriendsList = queryFunc("SELECT friends_array,profile_pic FROM users WHERE user_id='$userID'");
        // $friendsList = isRecord($queryFriendsList);
        // $friendsListSeparated = explode(',', $friendsList['friends_array']);
        // // notification for each friend
        // for ($i = 0; $i< sizeof($friendsListSeparated)-1;$i++) {
        //     $friend_id = $friendsListSeparated[$i];
        //     notification($userID,$friend_id,$postID,'post');
        // }
    }
}

function deletePost($postID)
{
    // Deleting post selected by passed postID
    $deleteQuery = queryFunc("DELETE from posts WHERE post_id ='$postID'");

    // Deleting comments of that post too
    queryFunc("DELETE from comments WHERE post_id ='$postID'");

    //Deleting likes of that post
    queryFunc("DELETE from likes WHERE post_id ='$postID'");

    //Deleting notifications of that post
    queryFunc("DELETE FROM notifications WHERE post_id='$postID'");

    // Returning success message
    $_SESSION['no_of_posts_changed']--;
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

    $ID = mysqli_insert_id($connection);

    
    //Generating Notification
    $whosePostQuery = queryFunc("SELECT user_id from posts where post_id='$postID'");
    $whosePost = isRecord($whosePostQuery);

    // Calling notification method for notification entry to database
    notification($userID, $whosePost['user_id'], $postID, 'commented');
    
    // // Query for getting the latest comment
    // $queryResult = queryFunc("SELECT comment_id from comments ORDER BY comment_id DESC LIMIT 1");
    // $row = isRecord($queryResult);

    // Returning the latest comemnt ID
    // return $row['comment_id'];
    return $ID;
}

function showPostsQueries($exception)
{
    $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id {$exception}");

    return $queryResult;
}

function showPostQuery($flag)
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
        // $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id order by post_id desc");

        $queryResult = showPostsQueries('order by post_id desc');
    } elseif ($flag == 'b') {
        // $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = {$userID} order by post_id desc");

        $queryResult = showPostsQueries("where users.user_id = {$userID} order by post_id desc");
    } elseif ($flag=='c') {
        $postID = $_SESSION['notiPostID'];
        // $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE post_id='$postID'");

        $queryResult = showPostsQueries("WHERE post_id={$postID}");
    } elseif ($flag == 'd') {
        // $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE posts.user_id='$userID' order by post_id desc LIMIT 1");

        $queryResult = showPostsQueries("WHERE posts.user_id={$userID} order by post_id desc LIMIT 1");
    } elseif ($flag > 0) {
        // $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = '$flag' order by post_id desc");

        $queryResult = showPostsQueries("where users.user_id = {$flag} order by post_id desc");
    }

    return $queryResult;
}


function showPosts($flag, $page, $limit)
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



    // start -> Where to start showing posts from
    // limit - limit of posts to show on one page i.e is max 10
    // Page - Current iteration of posts rendering or collection of 10 posts rendered treated as 1
    //$_SESSION['no_of_posts_changed'] - to keep track of no of posts added or deleted before page refreshing

    if ($page == 1) { // if you are at first page then starting with post 0
        $start = 0;
    } else { // else calculating which post to start from
        $start = ($page - 1) * $limit + $_SESSION['no_of_posts_changed'];
    }


    $userID = $_SESSION["user_id"];

    // Getting query Result
    $queryResult = showPostQuery($flag);
   
    // Profile Pic query of you
    $profilePic =  getUserProfilePic($_SESSION['user_id']);

    
    if (isData($queryResult)) {
        $numberOfIteration = 0; //Number of results checked - once it reaches to value of start we start rendering posts.
        $count = 1; // To keep track of no of posts rendered


        // If database returns something
        while ($row = isRecord($queryResult)) {
            //Wait to reach start value to start rendering posts, because before $start are already rendered

            if ($row['user_id'] == $_SESSION['user_id'] || isFriend($row['user_id'])) {
                //once it reaches to value of $start we start rendering posts.
                if ($numberOfIteration++ < $start) {
                    continue;
                }
                if ($count > $limit) {
                    break;
                } else {
                    $count++;
                }

                $postID = $row['post_id'];
                // $userID = $_SESSION['user_id'];
                $user = $_SESSION['user'];
                // $fUser = $row['user_id'];
                // $diffTime = differenceInTime($row['createdAt']);
                // $timeToShow = timeString($diffTime);

                $post = renderPost($row);
                $post .= renderPostComments($flag, $postID, $row['user_id']);
                $post .= renderPostCommentForm($postID, $user, $profilePic);

         
                // Finally rendering all the content in the variable xD
                echo $post;
            }
        }
        if ($count > $limit) {
            $infoForNextTime = "<input type='hidden' id='nextPage' value='".($page+1)."' ><input type='hidden' id='noMorePosts' value='false'>";
        } else {
            $infoForNextTime = "<input type='hidden' id='noMorePosts' value='true'>";
        }
        echo $infoForNextTime;
    }
}


function renderPostCommentForm($postID, $user, $profilePic)
{
       
                // Rendering input field for adding comment
    $post = <<<POST
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
    return $post;
}

function renderPostComments($flag, $postID, $fUser)
{
    // Opening comment section if it is a comment notification else not
    if ($flag == 'c' && $_SESSION['notiType'] == 'commented') {
        $commentShow = 'show';
    } else {
        $commentShow = 'hidden';
    }

    // Comment Section of a post
    $post = <<<POST
            <div id="comment-section-{$postID}" class='{$commentShow}'>
                <div class='comment-area-{$postID}'>

POST;

    // Querying database for the current post comments if any
    $commentResult = queryFunc("SELECT comments.user_id,comment_id,comment,CONCAT(first_name,' ',last_name) as 'name',createdAt,users.profile_pic from comments inner join users on users.user_id = comments.user_id where comments.post_id ='$postID'");

    while ($comments = isRecord($commentResult)) {
        $timeToShow = getTime($comments['createdAt']);
        $commentID = $comments['comment_id'];

        // Enabling delete option for comment if it is user's post or his comment else disabling
        if ($comments['user_id'] == $_SESSION['user_id'] || $_SESSION['user_id'] == $fUser) {
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

    return $post;
}

function renderPost($row)
{
    $postID = $row['post_id'];
    $timeToShow = getTime($row['createdAt']);


    // Getting likes count for the current post
    $NoOflikes = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
    $likes = isRecord($NoOflikes);


    // Getting number of comments for post
    $commentCountResult = queryFunc("SELECT count(*) as count from comments where post_id='$postID'");
    $commentsCount = isRecord($commentCountResult);

    //Getting liker's IDs
    $likers = queryFunc("SELECT user_id from likes where post_id='$postID'");
    $flag = false;
    if(isData($likers)){
        while($liker = isRecord($likers)){
            if($liker['user_id'] == $_SESSION['user_id']){
                $flag = true;
                break;
            }
        }
    }

    //Checking if you have liked the post?
    if ($flag) {
        $likeIcon = "<i class='blue far fa-thumbs-up'></i>";
    } else {
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
                    <a href='timeline.php?visitingUserID={$row['user_id']}'><img src='{$row['profile_pic']}' class='post-avatar post-avatar-40'/></a>

                    <div class='post-info'>
                    <a href='timeline.php?visitingUserID={$row['user_id']}' class='user'>{$row['name']}</a>
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

    return $post;
}

function logout()
{
    //Closing the session and destroying all the session variables
    // session_start();
    turnOffline($_SESSION['user_id']);
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
        } elseif ($time == 0) {
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

function showNotifications($flag,$page,$limit)
{
    // 1 - friend request dropdown
    // 10 - notification dropdown
    // all - notification page

    $noData = '';

    $user = $_SESSION['user_id'];

    if ($flag == 1) {
        $notiQuery = queryFunc("SELECT * from notifications WHERE (d_user_id='$user' or s_user_id='$user') AND typeC='request' order by noti_id desc");
        $postAvatar = 'post-avatar-30';
        $noData = 'No Friend Request';
    } 
    
    elseif ($flag==10) {
        // Selecting notifications for the current User
        $notiQuery = queryFunc("SELECT * from notifications WHERE d_user_id='$user' OR (s_user_id='$user' AND typeC='request') order by noti_id desc LIMIT 10");
        $postAvatar = 'post-avatar-30'; // For notification Area
    } else {
        $notiQuery = queryFunc("SELECT * from notifications WHERE d_user_id='$user' OR (s_user_id='$user' AND typeC='request')  order by noti_id desc");
        $postAvatar = 'post-avatar-40'; // For notification Page
    }

    if ($page == 1) { // if you are at first page then starting with post 0
        $start = 0;
    } 
    else { // else calculating which post to start from
        $start = ($page - 1) * $limit;
    }

    // flag for user realization
    // $isAny = false;
    
    if (isData($notiQuery)) {
        
        $numberOfIteration = 0; //Number of results checked - once it reaches to value of start we start rendering posts.
        $count = 1; // To keep track of no of posts rendered

        // If there are notifications
        $notiCounter = 0;
        
        while ($row = isRecord($notiQuery)) {

            //Wait to reach start value to start rendering posts, because before $start are already rendered

            //If defined number of posts are rendered then break
            //once it reaches to value of $start we start rendering posts.
            if ($numberOfIteration++ < $start) {
                continue;
            }
            if ($start + $limit == mysqli_num_rows($notiQuery)) {
                $count = 0;
            }
            if ($count > $limit) {
                break;
            } else {
                $count++;
            }

            $render = true;
                // $isAny = true;
            $sUser = $row['s_user_id'];
            $dUser = $row['d_user_id'];
            $postID = $row['post_id'];
            $type = $row['typeC'];
            $notiID = $row['noti_id'];
            $time = getTime($row['createdAt']);
            $colorNoti = '';

            $conflict = '';
            $notiIcon = '';

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
            $notiLink = "notification.php?postID=$postID&type=$type&notiID=$notiID";
                
            if($sUser == $user && $type=='request'){
                if($row['seen'] == 1 && $flag == 1){
                    $conflict = 'accepted your request';
                    $notiIcon = 'fas fa-check-circle';
                    $personQuery = queryFunc("SELECT profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$dUser'");
                    $sPerson = isRecord($personQuery);
                    
                }
                if($row['seen'] == 1 && $flag == 10){
                    $render = false;
                }
            }
             else if ($type=='post') {
                $conflict = 'posted';
                $notiIcon = 'far fa-user';
            } elseif ($type=='commented') {
                $conflict = 'commented on your post';
                $notiIcon = 'far fa-comment-dots';
            } elseif ($type == 'request'){
                $conflict = 'sent you a request';
                $notiIcon = 'fas fa-user-plus';
                $notiLink = "requests.php?notiID=$notiID";
            } else {
                $conflict = 'liked your post';
                $notiIcon = 'far fa-thumbs-up';
            }

            if($user != $sUser){
                // $personQuery = queryFunc("SELECT profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$sUser'");
                // $sPerson = isRecord($personQuery);
            }

            $noti = <<<NOTI
                <a href={$notiLink} class='notification  {$colorNoti}'>
                <span class='notification-image'>
                <img src='{$sPerson['profile_pic']}' class='post-avatar $postAvatar' />
                </span>
                <span class='notification-info'>
            <span class='notification-text'>{$sPerson['name']} has {$conflict}</span><i class='noti-icon {$notiIcon}'></i><span class='noti-time'>{$time}</span></span></a>
NOTI;
            if($render)
                echo $noti;
        }
        if($flag == 'all'){
            if ($count > $limit) {
                $infoForNextTime = "<input type='hidden' id='noMoreNotis' value='false'><input type='hidden' id='nextPageNotis' value='".($page+1)."' >";
            } else {
                $infoForNextTime = "<input type='hidden' id='noMoreNotis' value='true'>";
            }
            echo $infoForNextTime;
            }   
    }
    else{
        echo "<span class='center'>No Friend Request</span>";
    }
}


//Friend Functions

function isFriend($id)
{
    // Checking if specified user your friend or not?
    $userLoggedIn = $_SESSION['user_id'];
    $friend = queryFunc("SELECT friend_id FROM friends WHERE (user1='".$userLoggedIn."' and user2 = '".$id."') OR (user2='".$userLoggedIn."' and user1 = '".$id."') ");
    if (isData($friend)) {
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

function checkUserState($id)
{
    if (isFriend($id)) {
        // If person is already your friend
        $state = "<i class='fas fa-user-friends'></i><input type = 'Submit' name='remove_friend' value='Remove Friend'>";
    } elseif (reqSent($id)) {
        // If person hasn't accepted your request :(
        $state = "<i class='fas fa-reply'></i><input type = 'Submit' name='cancel_req' value='Friend Request Sent'>";
    } elseif (reqRecieved($id)) {
        //  If you haven't responded to the person's friend request xD
        $state = "<input type = 'Submit' name='respond_to_request' value='Respond to Friend Request'>";
    } else {
        // And if all above conditions fails, then people have no connection xD
        $state = "<i class='fas fa-user-plus'></i><input type = 'Submit' name='add_friend' value='Add Friend'>";
    }
    return $state;
}
function showFriendButton($id)
{
    // Showing add friend button if you came to someones else profile xD
    if ($id > 0 && $id != $_SESSION['user_id']) {
        // 2nd condition - You have not come to your profile through searching

        $button = "<form class='friend-button' action = 'timeline.php?visitingUserID=$id' method='POST'>";
        $button .= checkUserState($id);
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
    
    notification($_SESSION['user_id'], $id, 0, 'request');

    redirection("timeline.php?visitingUserID=".$id);
}

function cancelReq($id)
{
    $userID = $_SESSION['user_id'];
    // When cancel request button is clicked
    // Deleting friend request from database
    $friend = queryFunc("DELETE FROM friend_requests WHERE to_id =".$id." AND from_id =".$_SESSION['user_id']);
    queryFunc("DELETE from notifications where s_user_id='$userID' AND d_user_id='$id' AND typeC='request'");
    redirection("timeline.php?visitingUserID=".$id);
}

function removeFriend($id, $redirection = "")
{
    // When remove friend button is clicked
    // Removing friends and from both's records
    $userLoggedIn = $_SESSION['user_id'];
    queryFunc("delete from friends where (user1 = '$id' and user2 = '$userLoggedIn') OR (user1 = '$userLoggedIn' and user2 = '$id')");
    if ($redirection == "") {
        redirection("timeline.php?visitingUserID=".$id);
    }
}

// function updateFriendList($user, $friend)
// {
//     $friendArray = queryFunc("select friends_array from users where user_id =".$user);
//     $friendArray = isRecord($friendArray);
//     $friendArray = $friendArray['friends_array'];
//     // 1st -> search for this
//     // 2nd -> replace with this
//     // 3rd -> search in this
//     $newFriendsArray = str_replace($friend.",", "", $friendArray);
    
//     queryFunc("update users set friends_array ='". $newFriendsArray ."' where user_id =".$user);
// }

function acceptReq($id)
{
    // When you have accepted the friend request
    // $id of the user of whom you are accepting the request
    // Updating both users records
    $userLoggedIn = $_SESSION['user_id'];
    $friend = queryFunc("insert into friends (user1,user2,become_friends_at) VALUES ('$id','$userLoggedIn',now())");

    // Deleting request from the person record  who sent you the request,bcoz it is accepted
    queryFunc("UPDATE friend_requests SET status=1 WHERE to_id='{$userLoggedIn}' AND from_id='{$id}'");
}

function ignoreReq($id)
{
    $userLoggedIn = $_SESSION['user_id'];

    // Just simply deleting the request from sending user's record
    queryFunc("UPDATE friend_requests SET status=2 WHERE to_id='{$userLoggedIn}' AND from_id='{$id}'");
}

function displayFriends($count=null)
{
    // Displaying all friends of current user

    $userID = $_SESSION['user_id'];

    $queryResult = queryFunc("SELECT * from friends WHERE user1='$userID' OR  user2='$userID'");
    
    // Breaking the friends list in array
    
    //if ($friendsCount >= 1 ) {
    // if ($count == 10) {
    //     $expression = 10; // for friends area
    // } else {
    //     $expression = sizeof($friendsListSeparated)-1; //For request.php page
    // }
        
    // if($friendsCount >= $count){
    //     $expression = $count ? $count : sizeof($friendsListSeparated)-1;
    // }else{
    //     $expression = $friendsCount;
    // }
    $numberOfIteration = 0;
    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {
            if(isset($count)){
                if(++$numberOfIteration > $count){
                    $_SESSION['more_friends'] = 1;
                    break;
                }
            }
            $friend_id = ($_SESSION['user_id'] == $row['user1']) ? $row['user2'] : $row['user1'] ;  // friend
            // Getting name of that friend
            $queryFriends = queryFunc("SELECT *,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friend_id'");

            $friend = isRecord($queryFriends);

            $time = activeAgo($friend_id);

            $stateClass = 'state-off';

            if ($time == 'Just Now') {
                $time = 'Now';
                $stateClass = 'state-on';
            }
            
            // if ($friend['online'] == 0) {
            //     $state = "Offline";
            //     $stateClass = 'state-off';
            // } else {
            //     $state = "Online";
            //     $stateClass = 'state-on';
            // }

            $content = <<<FRIEND
                <div class="friend-container">
                <div class='friend'>
                <div class='friend-image'>
                <img class='post-avatar post-avatar-30' src='{$friend['profile_pic']}'  >
                </div>
                
                <div class='friend-info'>
                    <a href="timeline.php?visitingUserID={$friend['user_id']}" class='friend-text'>{$friend['name']}</a> 
                    <span class='{$stateClass}'>{$time}</span>           
                </div>
                <div class='friend-action'>
                <div>
                <a href="javascript:removeFriend({$friend['user_id']})" class='remove-friend'><i class="tooltip-container fas fa-times">
                <span class='tooltip tooltip-right'>Remove Friend</span>
                </i></a>
                </div>
            </div>
            </div>
            </div>
FRIEND;
            echo $content;
        }
    }
    if(!isset($_SESSION['more_friends'])){
        if ($numberOfIteration == 0) {
            $_SESSION['more_friends'] = 0;
        } else {
            $_SESSION['more_friends'] = 2;
        }
    }

}
//Message Functions
function sendMessage($user_to, $message_body)
{
    $user_from = $_SESSION['user_id'];
    $flag = 0;
    global $connection;
    $queryInsert = $connection->prepare("INSERT INTO messages (user_to, user_from, body, opened, viewed, deleted, dateTime) VALUES (?,?,?,?,?,?,now())");
    $queryInsert->bind_param("iisiii", $user_to, $user_from, $message_body, $flag, $flag, $flag);
    $queryInsert->execute();
    $queryInsert->close();
}

function getUserProfilePic($userID)
{
    $profilePicQuery= queryFunc("SELECT profile_pic from users where user_id='$userID'");
    $profilePicQueryResult = isRecord($profilePicQuery);
    return $profilePicQueryResult['profile_pic'];
}

function showMessages($partnerId, $page, $limitMsg)
{
    //Update opened to seen
    $userLoggedIn = $_SESSION['user_id'];
    $seen = queryFunc("update messages set opened = '1' where user_to = '$userLoggedIn'");
    $start = ($page - 1) * $limitMsg;

    $profilePicMe = getUserProfilePic($userLoggedIn);
    $profilePicYou = getUserProfilePic($partnerId);

    $getConvo = queryFunc("select * from messages where ((user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId')) AND deleted = 0 order by id desc");

    // echo '<script type="text/javascript"> scrollToLastMessage(); </script>';

    $numberOfIteration = 0; //Number of results checked
    $count = 1;

    $convoList = "";
    $flag = 0;
    while ($row = isRecord($getConvo)) {
        if ($flag == 0) {
            $_SESSION['last_msg_id'] = $row['id'];
            $flag = 1;
        }
        if ($numberOfIteration++ < $start) {
            continue;
        }
        //If defined number of posts are rendered then break
        if ($count > $limitMsg) {
            break;
        } else {
            $count++;
        }
        if ($numberOfIteration == mysqli_num_rows($getConvo)) {
            $count = 0;
        }
        if ($row['user_to'] == $userLoggedIn) {
            $type='their-message';
            $pic = $profilePicYou;
        } else {
            $type='my-message';
            $pic = $profilePicMe;
        }

        $time = getTime($row['dateTime']);

        $convo = <<<MESSAGE
        <div class='chat-message {$type}'>
            <img src='{$pic}' class='post-avatar post-avatar-30' />
            <span class='message'>{$row['body']}</span>
            <span class='message-time'>{$time}</span>
        </div>
MESSAGE;
         
        $convoList =  $convo . $convoList;
    }
    echo $convoList;
    if ($count > $limitMsg) {
        $infoForNextTime = "<input type='hidden' id='noMoreMessages' value='false'><input type='hidden' id='nextPageMessages' value='".($page+1)."' >";
    } else {
        $infoForNextTime = "<input  type='hidden' id='noMoreMessages' value='true'>";
    }
    echo $infoForNextTime;
}

function showRecentActivities($page,$limit,$flag = null){
    $userLoggedIn = $_SESSION['user_id'];
    if(isset($flag)){
        $activities = queryFunc("select * from recent_activities where user_id = '$userLoggedIn' order by activity_id desc limit 11");
    }
    else{
        $activities = queryFunc("select * from recent_activities where user_id = '$userLoggedIn' order by activity_id desc");
    }
    if ($page == 1) { // if you are at first page then starting with post 0
        $start = 0;
    } 
    else { // else calculating which post to start from
        $start = ($page - 1) * $limit;
    }
    $numberOfIteration = 0; // //Number of results checked - once it reaches to value of start we start rendering posts.
    $_SESSION['more_activities'] = 3;
    if(isData($activities)){
        $count = 1; // To keep track of no of posts rendered

        while ($row = isRecord($activities)) {
                
             //Wait to reach start value to start rendering posts, because before $start are already rendered

            //If defined number of posts are rendered then break
            //once it reaches to value of $start we start rendering posts.
            if ($numberOfIteration++ < $start) {
                continue;
            }
            if ($count > $limit) {
                $_SESSION['more_activities'] = 1;
                break;
            } else {
                $count++;
            }    
            addActivity($row['activity_type'], $row['activity_at_id'], $row['user_id']);
        }
        if(!(isset($flag))){
            if ($count > $limit) {
                $infoForNextTime = "<input type='hidden' id='noMoreActivities' value='false'><input type='hidden' id='nextPageActivities' value='".($page+1)."' >";
            } else {
                $infoForNextTime = "<input type='hidden' id='noMoreActivities' value='true'>";
            }
            echo $infoForNextTime;
        }
    }
    if($_SESSION['more_activities'] == 3){
        if ($numberOfIteration == 0) {
            $_SESSION['more_activities'] = 0;
        } else {
            $_SESSION['more_activities'] = 2;
        }
    }
}

function getRecentChatsUserIds()
{
    $recentConvos = array();
    //Getting ids of all the users where messages are received from
    $senderOfRecentMsgs = queryFunc("SELECT id,user_from,user_to,deleted FROM messages where ((user_to = ".$_SESSION['user_id']." or user_from = ".$_SESSION['user_id'].")) AND deleted = 0 ORDER BY id DESC ");
    $flag = 0;
    if (isData($senderOfRecentMsgs)) {
        while ($row = isRecord($senderOfRecentMsgs)) {
            if ($row['deleted'] == 0) {
                if ($flag == 0) {
                    $_SESSION['last_message_retrieved_for_recent_convos'] = $row['id'] ;
                    $flag = 1;
                }
                //if user logged in is the sender then store reciever's id, else store sender's id
                $idToPush = ($row['user_from'] == $_SESSION['user_id'] ? $row['user_to'] : $row['user_from']);
                //Check whether that sender is already in the list, if not, only then push his id
                // Pushing person into the list of recent chats area
                if (array_search($idToPush, $recentConvos) === false) {
                    array_push($recentConvos, $idToPush);
                }
            }
        }
        // Returning array of recent chat users
        return $recentConvos;
    }
    // If no messages were there
    return false;
}

function getUserFirstAndLastName($user_id)
{
    $user_name = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id=".$user_id);
    $user_name = isRecord($user_name);
    return $user_name['name'];
}

function getRecentChatsUsernames($recentConvos)
{
    // Getting names of users whose ids are passed
    $counter = 0;
    while ($counter < sizeof($recentConvos)) {
        $recentUser[$counter] = getUserFirstAndLastName($recentConvos[$counter]);
        $counter++;
    }
    return $recentUser;
}

function getProfilePicData($recentConvos)
{
    // Getting pic of users whose ids are passed
    $counter = 0;
    while ($counter < sizeof($recentConvos)) {
        $recentPic[$counter] = getUserProfilePic($recentConvos[$counter]);
        $counter++;
    }
    return $recentPic;
}

function getPartnersLastMessage($partnerId)
{
    $userLoggedIn = $_SESSION['user_id'];
    $details = queryFunc("SELECT user_from,body,dateTime from messages where ((user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId')) AND deleted = 0 order by id desc limit 1");
    $details = isRecord($details);
    if (strlen($details['body']) > 15) {
        $details['body'] = (substr($details['body'], 0, 15)."...");
    }
    return $details;
}
    
function showRecentChats()
{
    $recentUserIds = getRecentChatsUserIds(); //IDS of users
    if ($recentUserIds) {
        $recentUsernames = getRecentChatsUsernames($recentUserIds); // Names of users
        $recentProfilePics = getProfilePicData($recentUserIds); // Pics of users
        $counter = 0;
        while ($counter < sizeof($recentUsernames)) {
            $lastMessageDetails = getPartnersLastMessage($recentUserIds[$counter]);
            $from = $lastMessageDetails['user_from'];
            if ($from == $_SESSION['user_id']) {
                $from = "You : ";
            } else {
                // $from = getUserFirstAndLastName($from);
                $from = '';
            }
            $msg = $lastMessageDetails['body'];
            $at =  getTime($lastMessageDetails['dateTime']);
            $user = <<<DELIMETER
            <a href='messages.php?id={$recentUserIds[$counter]}' class='recent-user recent-user-{$recentUserIds[$counter]}'>
            
                <span class='recent-user-image'>
                    <img src='{$recentProfilePics[$counter]}' class='post-avatar post-avatar-40' />
                </span>
                <span class='recent-message-info'>
                    <span class="recent-username">{$recentUsernames[$counter]}</span>
                    <span class='recent-message-text'>{$from}{$msg}</span>
                    <span class='recent-message-time'>{$at}</span>
                </span>
                <span>
                <i class='tooltip-container far fa-trash-alt  comment-delete' onclick='javascript:deleteConvo({$recentUserIds[$counter]})'><span class='tooltip tooltip-left'>Delete</span></i>
                </span>
            </a> 
DELIMETER;
            echo $user;
            $counter++;
        }
    } else {
        $_SESSION['last_message_retrieved_for_recent_convos'] = 0;
    }
}

function searchUsersFortChats()
{
    $search = <<<DELIMETER
    <div class="search-message">
    <form action="" method="get" name="message_search_form">
    
    <input type="text"  onkeyup="getUsers(this.value,0)" name="q" placeholder="Search..." autocomplete = "off" id="message_search_text_input" class='search-message-input'>
   
      
    </form>
    <div class="search-result-message"></div>
    <div class="search-result-message-footer"></div>
  </div>
DELIMETER;
    echo $search;
}

function getSearchedUsers($value, $flag)
{
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
            <div class='search-person'>
            <div class='search-person-image'>
                <img src='{$row['profile_pic']}' class='post-avatar post-avatar-30' />
            </div>

                <a href='messages.php?id={$row['user_id']}' class='search-person-info'>
                    <span class='person-name'>
                        {$row['name']}
                    </span>
                </a>
            </div>
DELIMETER;
                    echo $user;
                }
            }
        } else {
            echo 'No';
        }
    }
}

function getRecentConvo()
{
    //Will get the ID of the person whom you recently had a chat with

    $userLoggedIn =$_SESSION['user_id'];
    $recentUser = queryFunc("SELECT user_to,user_from from messages where (user_to = ".$userLoggedIn." OR user_from = ".$userLoggedIn.") AND deleted = 0 order by id DESC limit 1");
    if (isData($recentUser)) {
        $recentUser = isRecord($recentUser);
        $recentPartnerId = ($recentUser['user_from'] == $userLoggedIn) ? $recentUser['user_to'] : $recentUser['user_from'];
        redirection("http://localhost/socioConnect/messages.php?id=".$recentPartnerId);
    }
}

function deleteConvo($partnerId)
{
    $userLoggedIn = $_SESSION['user_id'];
    queryFunc("update messages set deleted = 1 where (user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId')");
}


function profilePic($id)
{
    $queryResult = queryFunc("SELECT * FROM users WHERE user_id='$id'");
    $queryUser = isRecord($queryResult);
    $name = $queryUser['first_name'].' '.$queryUser['last_name'];

    $content =<<<PROFILE
    <div class='user-cover'>
        <div class='user-pic'>
            <span class='user-pic-container'>
            <img src='{$queryUser['profile_pic']}' onclick="showImage()" id="profile_picture"/>
            <span>
        </div>
PROFILE;
    if (isFriend($id) || $_SESSION['user_id'] == $id) {
        $content .=<<<PROFILE
    <div id="modal" class="modal">
            <span class="close" id="modal-close" onclick="onClosedImagModal()">&times;</span>
            <img class="modal-content" id="modal-img" src="">
        </div>
PROFILE;
    }
    $content .=<<<PROFILE
    </div>
    <div class='user-info'>
    <h3>{$name}</h3>
    <span>{$queryUser['email']}</span>
    </div>
PROFILE;
    
    echo $content;
}

function turnOnline($id)
{
    queryFunc("update users set online = 1 where user_id =".$id);
    queryFunc("UPDATE users set active_ago=0 WHERE user_id={$id}");
}

function turnOffline($id)
{
    queryFunc("update users set online = 0 where user_id =".$id);
    queryFunc("UPDATE users set active_ago=now() WHERE user_id={$id}");
}

function activeAgo($id)
{
    $queryResult = queryfunc("SELECT active_ago from users WHERE user_id={$id}");
    $timeResult = isRecord($queryResult);

    $time = getTime($timeResult['active_ago']);
    
    return $time;
}

function addActivity($activity_type, $target_id, $userLoggedIn)
{
    $userLoggedIn = $_SESSION['user_id'];
    $profilePic = getUserProfilePic($userLoggedIn);
    $deletedNoti = '';

    $flag = true;
    //To decide the noti incon
    if ($activity_type == 0) {
        $conflict = 'liked a post';
        $notiIcon = 'far fa-thumbs-up';
        $notiLink = "notification.php?postID={$target_id}&type=liked&notiID=0";
        $time = queryFunc("select createdAt from likes where post_id = $target_id and user_id = $userLoggedIn");
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['createdAt'];
        } else {
            $flag = false;
        }
    } elseif ($activity_type == 1) {
        $conflict = 'commented on a post';
        $notiIcon = 'far fa-comment-dots';
        $commentDetails = explode(" ", $target_id);
        $notiLink = "notification.php?postID=$commentDetails[0]&type=commented&notiID=0";
        $time = queryFunc("select createdAt from comments where comment_id = '$commentDetails[1]'");
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['createdAt'];
        } else {
            $flag = false;
        }
    } elseif ($activity_type == 2) {
        $conflict = 'added a post';
        $notiIcon = 'fas fa-pencil-alt';
        $notiLink = "notification.php?postID=$target_id&type=post&notiID=0";
        $time = queryFunc("select createdAt from posts where post_id = $target_id");
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['createdAt'];
        } else {
            $flag = false;
        }
    } elseif ($activity_type == 3) {
        $conflict = 'made a new friend';
        $notiIcon = 'fas fa-user-plus';
        $users = explode(" ", $target_id);
        $visitId = ($users[0] == $_SESSION['user_id']) ? $users[1] : $users[0] ;
        $notiLink = "timeline.php?visitingUserID=$visitId";
        $time = queryFunc("select become_friends_at from friends where (user1 = '$users[0]' AND user2 = '$users[1]') OR (user2 = '$users[1]' AND user1 = '$users[0]') ");
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['become_friends_at'];
        } else {
            $flag = false;
        }
    }
    if ($flag) {
        $time = getTime($time);
    } else {
        $time = "Deleted";
        $deletedNoti = 'deleted-noti';
        $notiLink = "javascript:void(0)";
    }
    $noti = <<<NOTI
        <a href={$notiLink} class='notification recent_activity {$deletedNoti}'>
            <span class='notification-image'>
                <img src='{$profilePic}' class='post-avatar post-avatar-30' />
            </span>
            <span class='notification-info'>
                <span class='notification-text'>You {$conflict}</span><i class='noti-icon {$notiIcon}'></i><span class='noti-time {$deletedNoti}'>{$time}</span>
            </span>
        </a>
NOTI;
    echo $noti;
}