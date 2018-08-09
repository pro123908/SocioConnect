<?php
require_once dirname(__FILE__) . '/db.php';
session_start();

function queryFunc($query)
{
    // Function to query the database for queries

    global $connection;
    $queryResult = mysqli_query($connection, $query);

    // If query fails
    if (!$queryResult) {
        die('Error in querying database ' . mysqli_error($connection));
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
    return (mysqli_fetch_assoc($queryResult));
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

    $hashed = $hash . $salt;

    // Creating hash of the passed string
    $string = crypt($string, $hashed);
    

    // Returning hashed string
    return $string;
}

function redirection($path)
{
    // Redirecting user to the specified path
    header('Location: ' . $path);
}

function addPost()
{
    // ---------------------- REFRACTED ------------------------

    // Adding post form
    $userID = $_SESSION['user_id'];
    $addPost = <<<DELIMETER
    <div class='show'>
        <div class='post-options'></div>
        <form action="" method='POST'>
            <textarea name="post" id="" cols="30" rows="10" placeholder='Share what you are thinking here' class="post-input"></textarea>
            <br>
            <div class='post-bottom'>
            <div class='upload-btn-wrapper'>
                <button class='pic-upload-btn'><i class='far fa-image'></i></button>
                <input type='file' name='post-pic' onchange='javascript:postPicSelected()'/>
                <span class='pic-name'></span>
            </div>
            <div class='post-btn-container'>
                <a href="javascript:addPost({$userID})" class='add-post-btn'>Post</a>
            </div>
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

function newPost($postContent, $pic = null,$isVideo=0,$extension=null)
{
    // ---------------------- REFRACTORED ------------------------
    global $connection;
    // Function for adding a post
    $post = clearString($postContent);
    // $post = mysqli_real_escape_string($connection, $postContent);
    $userID = $_SESSION['user_id'];

    
    // Inserting post data
    $queryResult = queryFunc("INSERT INTO posts(post,user_id,pic) VALUES('$post','$userID','$pic')");
    // ID of top inserted post
    $ID = mysqli_insert_id($connection);

    //Getting info of last inserted POST
    $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,posts.pic,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id WHERE post_id='$ID'");

    if ($queryResult = getRecordsFromQuery($queryResult)) {
        $postID = $queryResult['post_id'];
        $userID = $_SESSION['user_id'];
        $user = $_SESSION['user'];
        if($isVideo == 1){
        $postPic = "./assets/post_videos/".$queryResult['pic'];
        }else{
        $postPic = "./assets/post_pics/".$queryResult['pic'];
        }
        $profilePic = "./assets/profile_pictures/".$queryResult['profile_pic'];
        $timeToShow = getTime($queryResult['createdAt']);

        $PostDeleteButton = <<<PosDel
        <div class='post-delete-icon'>
        <i class="tooltip-container fas fa-edit" onclick="javascript:editPost({$postID})"><span class='tooltip tooltip-right'>Edit</span></i>
        <i onclick="javascript:deletePost({$postID})" class="tooltip-container fas fa-times"><span class='tooltip tooltip-right'>Remove</span></i>
        </div>
PosDel;

        /* Post Pic */
        $postPicContent = '';
        if($isVideo == 1){
            $postPicContent = <<<CONTENT
            <div class='post-image-container'>
                <video class='post-video' controls>
                    <source src="{$postPic}" type="video/{$extension}" >
                </video>
            </div>
CONTENT;
        }
        elseif($pic != null) {
            $postPicContent = <<<CONTENT
            <div class='post-image-container'>
            <img src='{$postPic}' class='post-image' />
            </div>
CONTENT;
        }

        $post = <<<POST
        <div class='post post-{$postID}'>
            <div class='post-content post-content-{$postID}'>
                {$PostDeleteButton}
                <div class='post-header'>
                    <a href='timeline.php?visitingUserID={$userID}'><img src='{$profilePic}' class='post-avatar post-avatar-40'/></a>

                    <div class='post-info'>
                        <a href='timeline.php?visitingUserID={$userID}' class='user'>{$queryResult['name']}</a>
                        <span class='post-time'>$timeToShow</span>
                        <span class='post-edited post-edited-{$postID}'></span>
                    </div>
                </div>

                <div class='show actual-post-{$postID}'>
                    <p>{$queryResult['post']}</p>
                    $postPicContent
                </div>
                <div class='post-stats'>
                    <span onmouseout='javascript:hideLikers({$postID})' onmouseover='javascript:likeUsers({$postID})' class='tooltip-container like-count like-count-{$postID}'><i class='like-count-icon far fa-thumbs-up'></i> 0</span>
                    <span class='tooltip tooltip-bottom count'></span>
                    <a href="javascript:showCommentField({$postID})" class='comment-count'><i class='far fa-comment-dots comment-count-{$postID}'></i> 0</a>
                </div>
            </div>
            <div class='post-buttons'>
                <a class='post-btn like-btn' href='javascript:like({$postID})'><i class='far fa-thumbs-up'></i> Like</a>
                <a  class='post-btn comment-btn' href="javascript:showCommentField({$postID})"><i class="far fa-comment-dots"></i> Comment</a>
            </div>
            <div id="comment-section-{$postID}" class='hidden'>
                <div class='comment-area-{$postID}'></div>
                <div class='comment-form comment-form-$postID'>
                <div class='user-image'>
                <img src='$profilePic' class='post-avatar post-avatar-30' />
            </div>
                    <form onsubmit="return comment({$postID},'{$user}','{$profilePic}')" method="post" id='commentForm'>
                        <input name = "comment_{$postID}" type='text' autocomplete = "off">
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
    // ---------------------- REFRACTED ------------------------

    // Deleting post selected by passed postID
    $deleteQuery = queryFunc("DELETE from posts WHERE post_id ='$postID'");

    // Deleting comments of that post too
    queryFunc("DELETE from comments WHERE post_id ='$postID'");

    //Deleting likes of that post
    queryFunc("DELETE from likes WHERE post_id ='$postID'");

    //Deleting notifications of that post
    queryFunc("DELETE FROM notifications WHERE post_id='$postID'");

    // Post Decreased
    $_SESSION['no_of_posts_changed']--;

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
    // ---------------------- REFRACTED ------------------------

    // userID - Person who commented
    // postID - Post on which person commented
    // comment - Comment text

    // Adding comment
    global $connection;
    //Inserting the comment using different method
    // $queryInsert = $connection->prepare("INSERT INTO comments (user_id, post_id, comment,inserted,createdAt) VALUES (?, ?, ?,?,now())");
    // $queryInsert->bind_param("iisi", $userID, $postID, $comment,1);
    // $queryInsert->execute();
    // $queryInsert->close();

    $queryComment = queryFunc("INSERT INTO comments (user_id, post_id, comment,createdAt) VALUES('$userID','$postID','$comment',now())");

    // Gettting the ID of last inserted record
    $ID = mysqli_insert_id($connection);

    //Generating Notification
    // Getting the creator of the post
    $whosePostQuery = queryFunc("SELECT user_id from posts where post_id='$postID'");
    $whosePost = isRecord($whosePostQuery);

    // Calling notification method for notification entry to database
    notification($userID, $whosePost['user_id'], $postID, 'commented');

    return $ID;
}

function showPostsQueries($exception)
{
    // Selecting posts from database based on exception passed
    $queryResult = queryFunc("SELECT post,post_id,posts.user_id,users.profile_pic,posts.pic,edited,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id {$exception}");

    return $queryResult;
}

function showPostQuery($flag)
{
    // ---------------------- REFRACTORED ------------------------

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

    if ($flag == 'a') {
        //Getting all posts
        $queryResult = showPostsQueries('order by post_id desc');
    } elseif ($flag == 'b') {

        // Getting only user's posts
        $queryResult = showPostsQueries("where users.user_id = {$userID} order by post_id desc");
    } elseif ($flag == 'c') {
        $postID = $_SESSION['notiPostID'];
        // To display notification on separate page with post
        $queryResult = showPostsQueries("WHERE post_id={$postID}");
    } elseif ($flag == 'd') {

        $queryResult = showPostsQueries("WHERE posts.user_id={$userID} order by post_id desc LIMIT 1");
    } elseif ($flag > 0) {
        // When you go to someone elses profile
        $queryResult = showPostsQueries("where users.user_id = {$flag} order by post_id desc");
    }

    return $queryResult;
}

function showPosts($flag, $page, $limit)
{
    // ------------------ REFACTORED -------------------------

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
    $profilePic = getUserProfilePic($_SESSION['user_id']);

    if (isData($queryResult)) {
        $numberOfIteration = 0; //Number of results checked - once it reaches to value of start we start rendering posts.
        $count = 1; // To keep track of no of posts rendered

        // If database returns something
        while ($row = isRecord($queryResult)) {
            //Wait to reach start value to start rendering posts, because before $start are already rendered

            // Showing only friends posts and your posts
            if ($row['user_id'] == $_SESSION['user_id'] || isFriend($row['user_id'])) {

                //once it reaches to value of $start we start rendering posts.
                if ($numberOfIteration++ < $start) {
                    continue;
                }
                if ($count > $limit) {
                    // Break if 10 posts are rendered
                    break;
                } else {
                    $count++;
                }

                $postID = $row['post_id'];

                $user = $_SESSION['user']; // username

                $post = renderPost($row); // For rendering post
                $post .= renderPostComments($flag, $postID, $row['user_id']); // For rendering comments of that post
                $post .= renderPostCommentForm($postID, $user, $profilePic); // Comment form for that post

                // Finally rendering all the content in the variable xD
                echo $post;
            }
        }
        if ($count > $limit) {
            // If there are more posts after limit
            $infoForNextTime = "<input type='hidden' id='nextPage' value='" . ($page + 1) . "' ><input type='hidden' id='noMorePosts' value='false'>";
        } else {
            $infoForNextTime = "<input type='hidden' id='noMorePosts' value='true'>";
        }

        echo $infoForNextTime;
    }

    $lastCommentRendered = queryFunc("SELECT comment_id from comments order by comment_id desc limit 1");
    $lastComment = isRecord($lastCommentRendered);

    $_SESSION['last_comment_id'] = $lastComment['comment_id'];

    $lastLikeRendered = queryFunc("SELECT like_id from likes order by like_id desc limit 1");
    $lastLike = isRecord($lastLikeRendered);

    $_SESSION['last_like_id'] = $lastLike['like_id'];

    $lastNotiRendered = queryFunc("SELECT noti_id from notifications order by noti_id desc limit 1");
    $lastNoti = isRecord($lastNotiRendered);

    $_SESSION['last_noti_id'] = $lastNoti['noti_id'];

}

function renderPostCommentForm($postID, $user, $profilePic)
{
    // ---------------------- REFRACTORED ------------------------

    // Rendering input field for adding comment
    $post = <<<POST
            </div>
            <div class='comment-form comment-form-$postID'>
                <div class='user-image-comment'>
                    <img src='$profilePic' class='post-avatar post-avatar-30' />
                </div>
                <form onsubmit="return comment({$postID},'{$user}','{$profilePic}')" method="post" id='commentForm{$postID}'>
                    <input name = "comment_{$postID}" type='text' autocomplete = "off">
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
    // ---------------------- REFRACTORED ------------------------

    // flag - notification type
    // fUser - creator of post

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
    $commentResult = queryFunc("SELECT comments.user_id,comment_id,comment,CONCAT(first_name,' ',last_name) as 'name',createdAt,users.profile_pic,edited from comments inner join users on users.user_id = comments.user_id where comments.post_id ='$postID'");

    // Number of comments of post
    $numberOfComments = mysqli_num_rows($commentResult);
    $commentCounter = 0;

    while ($comments = isRecord($commentResult)) {
        $timeToShow = getTime($comments['createdAt']);
        $commentID = $comments['comment_id'];
        $DP = "./assets/profile_pictures/" .$comments['profile_pic'];

        // Enabling delete option for comment if it is user's post or his comment else disabling
        if ($comments['user_id'] == $_SESSION['user_id'] || $_SESSION['user_id'] == $fUser) {
            $commentDeleteButton = <<<ComDel
        <i class='tooltip-container fas fa-times comment-delete' onclick='javascript:deleteComment({$commentID})'><span class='tooltip tooltip-right'>Remove</span></i>
ComDel;
        } else {
            $commentDeleteButton = '';
        }

        // Enabling edit option for comment if it is user's comment else disabling
        if ($comments['user_id'] == $_SESSION['user_id']) {
            $commentEditButton = <<<ComEdit
            <i class="tooltip-container fas fa-edit comment-edit" onclick="javascript:editComment({$commentID},{$postID},'{$DP}','{$timeToShow}')"><span class='tooltip tooltip-right'>Edit</span></i>
ComEdit;
        } else {
            $commentEditButton = '';
        }

        // if comment is edited then display 'edited' text
        if ($comments['edited'] == 1) {
            $edited = "Edited";
        } else {
            $edited = "";
        }

        // Rendering comment
        $post .= <<<POST
                    <div class='comment comment-{$commentID}'>

                        <div class='user-image'>
                            <a href='timeline.php?visitingUserID={$comments['user_id']}'><img src='{$DP}' class='post-avatar post-avatar-30' /></a>
                        </div>

                        <div class='comment-info'>
                        {$commentDeleteButton}
                        {$commentEditButton}
                        <div class='comment-body'>
                        <a href='timeline.php?visitingUserID={$comments['user_id']}' class='comment-user'>{$comments['name']} : </a>
                        <span class='comment-text'>{$comments['comment']}</span>
                        <span class='comment-time'>$timeToShow</span>
                        <span class='comment-edit-text'>$edited</span>
                        </div>
                        </div>
                    </div>

POST;
    }

    return $post;
}

function renderPost($row)
{
    // ---------------------- REFRACTORED ------------------------

    // row - All content about post

    $picOrVideo = explode(".",$row['pic']);
    $mediaFlag = 0;
    if(isset($picOrVideo[1])){
        $picOrVideo = $picOrVideo[1];
        if($picOrVideo == "mp4" || $picOrVideo == "flv" || $picOrVideo == "avi"){
            $postPic = "./assets/post_videos/" . $row['pic'];
            $mediaFlag = 1;
        }else{
            $postPic = "./assets/post_pics/" . $row['pic'];
            $mediaFlag = 2;
        }  
    }else{
        $postPic = "./assets/post_pics/" . $row['pic'];
    }
    

    $postID = $row['post_id'];
    $timeToShow = getTime($row['createdAt']);
    
    $userLoggedIn = $_SESSION['user_id'];
    $row['profile_pic'] = "./assets/profile_pictures/" . $row['profile_pic'];

    // Getting likes count for the current post
    $NoOflikes = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
    $likes = isRecord($NoOflikes);

    // Getting number of comments for post
    $commentCountResult = queryFunc("SELECT count(*) as count from comments where post_id='$postID'");
    $commentsCount = isRecord($commentCountResult);

    //Getting liker's IDs - If you have liked that post or not?
    $likers = queryFunc("SELECT user_id from likes where post_id='$postID' and user_id = '$userLoggedIn'");

    $flag = false;

    if (isData($likers)) {
        $flag = true; // You have liked the current post
    }

    //Checking if you have liked the post?
    if ($flag) {
        $likeIcon = "<i class='blue far fa-thumbs-up'></i>";
    } else {
        $likeIcon = "<i class='far fa-thumbs-up'></i>";
    }

    // Enabling delete and edit option for post if it is current user's post else disabling
    if ($row['user_id'] == $_SESSION['user_id']) {
        $PostDeleteButton = <<<PosDel
            <div class='post-delete-icon'>
                <i class="tooltip-container fas fa-edit" onclick="javascript:editPost({$postID})"><span class='tooltip tooltip-right'>Edit</span></i>
                <i onclick="javascript:deletePost({$postID})" class="tooltip-container fas fa-times"><span class='tooltip tooltip-right'>Remove</span></i>
            </div>
PosDel;
    } else {
        $PostDeleteButton = '';
    }

    /* Post Pic */
    $postPicContent = '';

    if($mediaFlag == 1){
        $postPicContent = <<<CONTENT
        <div class='post-image-container'>
            <video class='post-video' controls>
                <source src="{$postPic}" >
            </video>
        </div>
CONTENT;
    }
    elseif ($mediaFlag == 2) {
        // if there is a post pic
        $postPicContent = <<<CONTENT
         <div class='post-image-container'>
         <img src='{$postPic}' class='post-image'  />
         </div>
CONTENT;
    }

    // If post is edited then displaying 'edited' text
    if ($row['edited'] == 1) {
        $edited = "Edited";
    } else {
        $edited = "";
    }

    // Rendering Post
    $post = <<<POST
                <div class='post post-{$postID}'>
                    <div class='post-content post-content-{$postID}'>
                    {$PostDeleteButton}
                    <div class='post-header'>
                    <a href='timeline.php?visitingUserID={$row['user_id']}'><img src='{$row['profile_pic']}' class='post-avatar post-avatar-40'/></a>

                    <div class='post-info'>
                    <a href='timeline.php?visitingUserID={$row['user_id']}' class='user'>{$row['name']}</a>
                    <span class='post-time'>$timeToShow</span>
                    <span class='post-edited post-edited-{$postID}'>$edited</span>
                    </div>
                    </div>
                    <div class='show actual-post-{$postID}'>
                        <p>{$row['post']}</p>
                        $postPicContent
                    </div>
                    <div class='post-stats'>
                    <span onmouseout='javascript:hideLikers({$postID})' onmouseover='javascript:likeUsers({$postID})' class='tooltip-container like-count like-count-{$postID}'><i class='like-count-icon far fa-thumbs-up'></i> {$likes['count']}
                    <span class='tooltip tooltip-bottom count'></span>
                    </span>
                    <a href="javascript:showCommentField({$postID})" class='comment-count'><i class='far fa-comment-dots comment-count-{$postID}'></i> {$commentsCount['count']}</a>
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
    // --------------------- REFACTORED -----------------------

    turnOffline($_SESSION['user_id']);

    session_start();

    // Unset all of the session variables.
    $_SESSION = array();

    // If it's desired to kill the session, also delete the session cookie.
    // Note: This will destroy the session, and not just the session data!
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finally, destroy the session.
    session_destroy();

    // Redirecting to the login page
    redirection('../../index.php');
}

function differenceInTime($createdAt)
{
    // Calculating difference in current time and time of the particular content
    $currentTime = queryFunc("SELECT TIMESTAMPDIFF(SECOND, '$createdAt', now()) as 'time' ");
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
            return $time . " Second Ago";
        } elseif ($time == 0) {
            return "Just Now";
        } else {
            return $time . " Seconds Ago";
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
    elseif ($time > 86399 && $time < 604800) {
        // if it is just one day
        if (($time / 86400) < 2) {
            return floor($time / 86400) . " Day Ago";
        } else {
            return floor($time / 86400) . " Days Ago";
        }
    }
    // Time in weeks
    elseif ($time > 604799 && $time < 2628000) {
        // if it is just one week
        if (($time / 604800) < 2) {
            return floor($time / 604800) . " Week Ago";
        } else {
            return floor($time / 604800) . " Weeks Ago";
        }
    }
    // Time in months
    elseif ($time > 2627999 && $time < 31557600) {
        // if it is just one month
        if (($time / 2628000) < 2) {
            return floor($time / 2628000) . " Month Ago";
        } else {
            return floor($time / 2628000) . " Months Ago";
        }
    }
    elseif($time > 31557599){
        if(($time / 31557600) < 2){
            return floor($time / 31557600) . " Year";
        }else{
            return floor($time / 31557600) . " Years";
        }
    }
}

function formValidation($email, $pass, $re_pass,$age)
{
    /* --------------------- REFACTORED ----------------------- */

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

    $thenDate = $age;
    $currentDate = date('d-m-Y');

    $diff = abs(strtotime($currentDate) - strtotime($thenDate));

    $years = floor($diff / (365*60*60*24));

        if ($pass != $re_pass || $row['user_id'] > 0 || preg_match("/[0-9]+/", $pass) == 0 || preg_match("/[A-Za-z]+/", $pass) == 0 || $years < 13) {
        if ($row['user_id'] > 0) {
            $_SESSION['s_email_error'] = "Email Already in Use";
        } else {
            $_SESSION['s_email_error'] = "";
        }
        if ($pass != $re_pass) {
            $_SESSION['s_pass_error'] = "Passwords Don't Match";
        } elseif (preg_match("/[0-9]+/", $pass) == 0 || preg_match("/[A-Za-z]+/", $pass) == 0) {
            $_SESSION['s_pass_error'] = "Password Must Contain Alphanumeric Characters";
        } else {
            $_SESSION['s_pass_error'] = "";
        }

        if($years < 13){
            $_SESSION['s_age_error'] = 'Not old enough!';
        }

        
        // If validation is unsuccessful
        return false;
    } else {
        // If validation is successful
        return true;
    }
}

function notification($sUser, $dUser, $postID, $type)
{
    /* ---------------------- REFRACTORED ------------------------ */

    // sUser - Person who generated notification
    // dUser - Person to which notification will be sent
    // post - post on which notification is occured
    // type - type of notification - like,comment

    //Checking if notification already been there and is seen
    // To avoid multiple notifications for comment on post
    // Such as commenting two times on post in one go
    $notiAlready = queryFunc("SELECT * from notifications WHERE s_user_id='$sUser' AND post_id='$postID' AND typeC='$type' AND d_user_id='$dUser' AND seen != 1");

    if (!isData($notiAlready)) {
        //Checking if the src and dest user are not same
        // Avoiding generating notification to yourself
        if ($sUser != $dUser) {
            $notiQuery = queryFunc("INSERT INTO notifications(s_user_id,d_user_id,post_id,typeC,createdAt) VALUES({$sUser}, '$dUser','$postID','$type',now())");
        }
    }
}

function notificationQuery($conflict, $limit = 0)
{

    // limit - Number of notifications to render
    $isLimit = '';

    if ($limit != 0) {
        $isLimit = "LIMIT $limit";
    }

    $notiQuery = queryFunc("SELECT * from notifications WHERE $conflict order by noti_id desc $isLimit ");

    return $notiQuery;
}

function showNotifications($place, $page, $limit, $ajax = false)
{
    /* --------------- REFACTORED -------------------- */

    // $limit - Number of notifications

    // $page - Notification Page Number -> on which page you are

    // $place
    // 1 - friend request dropdown
    // 2 - notification dropdown
    // 3 - notification page

    $user = $_SESSION['user_id'];

    // If no notifications are found then to display a message
    $ifNoData = '';

    if ($place == 1) {
        // Getting all the requests from database
        // Requests accepted by you and requests sent by you
        $notiQuery = notificationQuery("(d_user_id='$user' or s_user_id='$user') AND typeC='request'", $limit);
        $postAvatar = 'post-avatar-30';
        $ifNoData = '<h3>No Friend Requests</h3>';
    } elseif ($place == 2) {
        // Selecting notifications for the current User
        // Notification generated for you or friend request sent by you
        $notiQuery = notificationQuery("d_user_id={$user} OR (s_user_id={$user} AND typeC='request')", $limit);
        $postAvatar = 'post-avatar-30'; // For notification Area
        $ifNoData = '<h3>No Notifications</h3>';

    } elseif ($place == 3) {
        // For notificaton page
        $notiQuery = notificationQuery("d_user_id={$user} OR (s_user_id={$user} AND typeC='request')");
        $postAvatar = 'post-avatar-40'; // For notification Page
        $ifNoData = '<h3>No Notifications</h3>';
    }

    if ($page == 1) { // if you are at first page then starting with post 0
        $start = 0;
    } else { // else calculating which post to start from
        $start = ($page - 1) * $limit;
    }

    if (isData($notiQuery)) {

        if ($place == 1) {
            $friendRequestText = '<h3>Friend Requests</h3>';
            echo $friendRequestText;
        } else if ($place == 2) {
            $notificationText = '<h3>Notifications</h3>';
            echo $notificationText;
            echo "<div class='notifications-dropdown'>";
        }

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

            $sUser = $row['s_user_id'];
            $dUser = $row['d_user_id'];
            $postID = $row['post_id'];
            $type = $row['typeC'];
            $notiID = $row['noti_id'];
            $time = getTime($row['createdAt']);
            $colorNoti = '';
            $conflict = '';
            $notiIcon = '';

            // Will explain this xD
            if ($type == 'request' && $sUser == $user && $row['seen'] != 1) {
                continue;
            }

            // Keeping ID of last notification so we can load latest notifications through AJAX
            // if ($notiCounter == 0) {
            //     $_SESSION['last_noti_id'] = $notiID;
            //     $notiCounter = 1;
            // }

            // Changing notification color if it hasn't been seen
            if ($row['seen'] == 0) {
                $colorNoti = 'noSeen';
            }

            // for selecting user pic
            $queryID = $sUser;

            // Checking for request(accepted) notifications
            if ($sUser == $user) {
                if ($row['seen'] == 1 && ($place == 1 || $place == 2 || $place == 3)) {
                    $conflict = 'accepted your request';
                    $notiIcon = 'fas fa-check-circle';
                    $notiLink = "timeline.php?visitingUserID=$dUser";
                    //Modifying queryID because we are selecing different user's pic this time
                    $queryID = $dUser;

                }

            } else if ($type == 'post') {
                $conflict = 'posted';
                $notiIcon = 'far fa-user';
            } elseif ($type == 'commented') {
                $conflict = 'commented on your post';
                $notiIcon = 'far fa-comment-dots';
            } elseif ($type == 'request') {
                $conflict = 'sent you a request';
                $notiIcon = 'fas fa-user-plus';
                $notiLink = "requests.php?notiID=$notiID";
            } else {
                $conflict = 'liked your post';
                $notiIcon = 'far fa-thumbs-up';
            }

            // Selecting name of the user who generated the notification
            $personQuery = queryFunc("SELECT profile_pic,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$queryID'");
            $sPerson = isRecord($personQuery);

            if ($type != 'request') {
                $notiLink = "notification.php?postID=$postID&type=$type&notiID=$notiID";
            }
            $sPerson['profile_pic'] = "./assets/profile_pictures/" . $sPerson['profile_pic'];
            $noti = <<<NOTI
                <a href={$notiLink} class='notification  {$colorNoti}'>
                <span class='notification-image'>
                <img src='{$sPerson['profile_pic']}' class='post-avatar $postAvatar' />
                </span>
                <span class='notification-info'>
            <span class='notification-text'>{$sPerson['name']} has {$conflict}</span><i class='noti-icon {$notiIcon}'></i><span class='noti-time'>{$time}</span></span></a>
NOTI;

            echo $noti;
        }

        if ($place == 2) {
            echo "</div>";
        }

        // When notification dropdown
        if ($place == 2) {
            $notificationSeeMore = <<<DATA
        <a href="allNotification.php" class='see-more'>
              <span>See more</span>
        </a>
DATA;
            echo $notificationSeeMore;

        } elseif ($place == 1) {
            // When Friend Request dropdown
            $friendRequestSeeMore = <<<DATA
            <a href="requests.php" class='see-more'>
            <span>See more</span>
          </a>
DATA;
            echo $friendRequestSeeMore;
        }

        if ($place == 3) {
            // If page is full with notification limit
            if ($count > $limit) {
                $infoForNextTime = "<input type='hidden' id='noMoreNotis' value='false'><input type='hidden' id='nextPageNotis' value='" . ($page + 1) . "' >";
            } else {
                // If there were no more records
                $infoForNextTime = "<input type='hidden' id='noMoreNotis' value='true'>";
            }
            echo $infoForNextTime;
        }
    }
    // If no records are there to render
    else {
        echo "<span class='center'>{$ifNoData}</span>";
    }
}

//Friend Functions

function isFriend($id)
{
    // Checking if specified user your friend or not?
    $userLoggedIn = $_SESSION['user_id'];
    $friend = queryFunc("SELECT friend_id FROM friends WHERE (user1='{$userLoggedIn}' and user2 = {$id}) OR (user2={$userLoggedIn} and user1 = {$id}) ");
    if (isData($friend)) {
        return true;
    } else {
        return false;
    }
}

function showMutualFriends($visitingId){
    $counter = 0;
    $friends = queryFunc("SELECT * from friends where user1 = $visitingId OR user2 = $visitingId order by friend_id DESC");
    if(isData($friends)){
        while($row = isRecord($friends)){
            $idToCheck = $visitingId == $row['user1'] ? $row['user2'] : $row['user1']; 
            if(isFriend($idToCheck)){
                $user = queryFunc("SELECT CONCAT(first_name, ' ', last_name) as name, user_id, profile_pic from users where user_id = $idToCheck");
                $user = isRecord($user);
                $time = activeAgo($user['user_id']);
                $user['profile_pic'] = "./assets/profile_pictures/" . $user['profile_pic'];
                $stateClass = 'state-off';
                if ($time == 'Just Now') {
                    $time = 'Now';
                    $stateClass = 'state-on';
                }
                $content = <<<USER
                <div class='mutual-friend'>
                    <div class='mutual-friend-image'>
                        <img class='post-avatar post-avatar-30' src='{$user['profile_pic']}' >
                    </div>
                    <div class='mutual-friend-info'>
                        <a href="timeline.php?visitingUserID={$user['user_id']}" class='mutual-friend-text'>{$user['name']}</a>
                        <span class='{$stateClass}'>{$time}</span>
                    </div>
                </div>
USER;
                echo $content;   
                $counter++;
                if($counter >= 10)
                    break;     
            }
        }
        if($counter == 0)
            echo "<p class ='see-more'>No mutual Friends</p>";
    }
    else{
        echo "<p class ='see-more'>No mutual Friends</p>";
    }
}


function reqSent($id)
{

    // Checking if request is already sent?
    $request = queryFunc("SELECT id from friend_requests where to_id ={$id} AND from_id={$_SESSION['user_id']} AND status = 0");
    if (isRecord($request)) {
        return true;
    } else {
        return false;
    }
}

function reqRecieved($id)
{
    // Checking if you have received the request from that person?
    $request = queryFunc("SELECT id from friend_requests where to_id ={$_SESSION['user_id']} AND from_id={$id} AND status = 0");
    if (isRecord($request)) {
        return true;
    } else {
        return false;
    }
}

function checkUserState($id)
{
    // Checking if person whom profile you visited is your friend or not?

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
    if(checkUserRequests()){
        $friend = queryFunc("INSERT INTO friend_requests (to_id, from_id) values({$id},{$_SESSION['user_id']})");
        
        notification($_SESSION['user_id'], $id, 0, 'request');
        
        redirection("timeline.php?visitingUserID=" . $id);
    }
    else{
        echo "<script>alert('You have reached your limit of number of friend requests allowed for a single account')</script>";
    }

}

function cancelReq($id)
{
    $userID = $_SESSION['user_id'];
    // When cancel request button is clicked
    // Deleting friend request from database
    $friend = queryFunc("DELETE FROM friend_requests WHERE to_id ={$id} AND from_id ={$_SESSION['user_id']}");

    queryFunc("DELETE from notifications where s_user_id='$userID' AND d_user_id='$id' AND typeC='request'");
    redirection("timeline.php?visitingUserID=" . $id);
}

function removeFriend($id, $redirection = "")
{
    // When remove friend button is clicked
    // Removing friends and from both's records
    $userLoggedIn = $_SESSION['user_id'];
    queryFunc("DELETE FROM friends where (user1 = '$id' and user2 = '$userLoggedIn') OR (user1 = '$userLoggedIn' and user2 = '$id')");
    if ($redirection == "") {
        redirection("timeline.php?visitingUserID=" . $id);
    }
}

function acceptReq($id)
{
    // When you have accepted the friend request
    // $id of the user of whom you are accepting the request
    // Updating both users records
    $userLoggedIn = $_SESSION['user_id'];
    $friend = queryFunc("INSERT into friends (user1,user2,become_friends_at) VALUES ('$id','$userLoggedIn',now())");

    // Deleting request from the person record  who sent you the request,bcoz it is accepted
    queryFunc("UPDATE friend_requests SET status=1 WHERE to_id={$userLoggedIn} AND from_id={$id}");
}

function ignoreReq($id)
{
    $userLoggedIn = $_SESSION['user_id'];

    // Just simply deleting the request from sending user's record
    queryFunc("UPDATE friend_requests SET status=2 WHERE to_id={$userLoggedIn} AND from_id={$id}");
}

function displayFriends($count = null, $id = null)
{
    // id - some value if you have visited someone's else profile and click on friends
    // count - some value if limit is given for friends representation
    // Displaying all friends of current user

    if ($id) {
        $userID = $id;
    } else {
        $userID = $_SESSION['user_id'];
    }

    $queryResult = queryFunc("SELECT * from friends WHERE user1='$userID' OR  user2='$userID'");

    $numberOfIteration = 0;
    if (isData($queryResult)) {

        $friends = array();
        while ($row = isRecord($queryResult)) {
            if (isset($count)) {
                // if friends equal to count are rendered then break loop
                if (++$numberOfIteration > $count) {
                    $_SESSION['more_friends'] = 1; // See more friends
                    break;
                }
            }

            // Reason yet to be found!
            $friend_id = ($userID == $row['user1']) ? $row['user2'] : $row['user1']; // friend

            // $friend_id = $row['user2'];

            // Getting name of that friend
            $queryFriends = queryFunc("SELECT *,CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id='$friend_id'");

            $friend = isRecord($queryFriends);

            array_push($friends, $friend);
        }
        sortArrayByKey($friends, true);
        printFriendsList($friends, $id);
    }
    if (!isset($_SESSION['more_friends'])) {
        if ($numberOfIteration == 0) {
            $_SESSION['more_friends'] = 0; // No friends to show
        } else {
            $_SESSION['more_friends'] = 2; // No more friends to show
        }
    }

}

function printFriendsList($friends, $id)
{

    // id - some value if it is your profile

    foreach ($friends as $friend) {
        $time = activeAgo($friend['user_id']);

        $stateClass = 'state-off';

        if ($time == 'Just Now') {
            $time = 'Now';
            $stateClass = 'state-on';
        }
        $friend['profile_pic'] = "./assets/profile_pictures/".$friend['profile_pic'];
        $content = <<<FRIEND
            <div class="friend-container">
            <div class='friend-fix'>
            <a href="timeline.php?visitingUserID={$friend['user_id']}"  class='friend'>
            <span class='friend-image'>
            <img class='post-avatar post-avatar-30' src='{$friend['profile_pic']}'  >
            </span>

            <span class='friend-info'>
                <span class='friend-text'>{$friend['name']}</span>
                <span class='{$stateClass}'>{$time}</span>
            </span>
            </a>
            <div class='friend-action'>
            <div>
FRIEND;
        if (!$id || isFriend($friend['user_id'])) {
            $content .= <<<FRIEND
                <a href="javascript:removeFriend({$friend['user_id']})" class='remove-friend remove-friend-{$friend['user_id']}'><i class="tooltip-container fas fa-times">
                <span class='tooltip tooltip-left'>Remove Friend</span>
                </i></a>
FRIEND;
        } else if (reqRecieved($friend['user_id'])) {
            $content .= <<<FRIEND
            <a href="requests.php?id={$id}" class='remove-friend'><i class="tooltip-container fas fa-backward">
            <span class='tooltip tooltip-right'>Respond To Friend Request</span>
            </i></a>
FRIEND;
        } else if (reqSent($friend['user_id'])) {
            $content .= <<<FRIEND
            <a href="javascript:cancelReq({$friend['user_id']})" class='add-friend add-friend-{$friend['user_id']}'><i class="tooltip-container fas fa-check">
            <span class='tooltip tooltip-right'>Friend Request Sent</span>
            </i></a>
FRIEND;
        }
        else if($friend['user_id'] != $_SESSION['user_id']){
            $content .= <<<FRIEND
            <a href="javascript:addFriend({$friend['user_id']})" class='add-friend add-friend-{$friend['user_id']}'><i class="tooltip-container fas fa-plus">
            <span class='tooltip tooltip-right'>Add Friend</span>
            </i></a>
FRIEND;
        }

        $content .= <<<FRIEND
        </div>
        </div>
        </div>
        </div>
        
FRIEND;
        echo $content;
    }
}

function sortArrayByKey(&$array, $flag)
{
    /* Doesn't make sense to Bilal */

    if ($flag) {
        usort($array, function ($a, $b) {
            $comparison = strcmp(strtolower($a{'first_name'}), strtolower($b{'first_name'}));
            if ($comparison != 0) {
                return $comparison;
            } else {
                return strcmp(strtolower($a{'last_name'}), strtolower($b{'last_name'}));
            }

        });
    } else {
        usort($array, function ($a, $b) {
            return strcmp(strtolower($a{'name'}), strtolower($b{'name'}));
        });
    }
}

//Message Functions
function sendMessage($user_to, $user_from,$message_body)
{
    $flag = 0;
    $space = " ";
    $queryMessage = queryFunc("INSERT INTO messages (user_to, user_from, body, opened,deleted,dateTime) VALUES('$user_to','$user_from','$message_body','$flag','$space',now())");
    if($user_to == 2){
        $defaultMessage = "Hi, this is a default account. It's only purpose is to make your initial experience better on our platform. In case of any issues or bugs related to the website OR if someone is making you uncomfortable on the platform, feel free to report it to any of the admins along with a Screenshot of the problem, so that we can take appropriate actions. Happy Socializing :)";
        sendMessage($user_from,$user_to, clearString($defaultMessage));
    }
}

function getUserProfilePic($userID)
{
    // Will return the profile pic of user based upon ID passed

    $profilePicQuery = queryFunc("SELECT profile_pic from users where user_id=$userID");
    $profilePicQueryResult = isRecord($profilePicQuery);
    return "./assets/profile_pictures/" .$profilePicQueryResult['profile_pic'];
}

function showMessages($partnerId, $page, $limitMsg)
{
    //Update opened to seen
    $userLoggedIn = $_SESSION['user_id'];
    $seen = queryFunc("UPDATE messages set opened = '1' where user_to = '$userLoggedIn' AND user_from = '$partnerId'");

    $start = ($page - 1) * $limitMsg;

    $profilePicMe = getUserProfilePic($userLoggedIn);
    $profilePicYou = getUserProfilePic($partnerId);

    $check = $userLoggedIn . " ";
    $getConvo = queryFunc("SELECT * from messages where ((user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId')) AND deleted not like ' $userLoggedIn%' AND deleted not like '%$userLoggedIn ' order by id desc");


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

        //If defined number of messages are rendered then break
        if ($count > $limitMsg) {
            break;
        } else {
            $count++;
        }
        if ($numberOfIteration == mysqli_num_rows($getConvo)) {
            $count = 0;
        }

        //Checking whose message the current message is?
        if ($row['user_to'] == $userLoggedIn) {
            $type = 'their-message';
            $pic = $profilePicYou;
        } else {
            $type = 'my-message';
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

        //Current message concatenated with previous messages
        $convoList = $convo . $convoList;
    }
    echo $convoList;

    // If defined number of messages were rendered
    if ($count > $limitMsg) {
        $infoForNextTime = "<input type='hidden' id='noMoreMessages' value='false'><input type='hidden' id='nextPageMessages' value='" . ($page + 1) . "' >";
    } else {
        // If there were no more messages to rendered
        $infoForNextTime = "<input  type='hidden' id='noMoreMessages' value='true'>";
    }
    echo $infoForNextTime;
}

function getRecentChatsUserIds()
{
    // Getting IDS of users you recently chat with
    $userLoggedIn = $_SESSION['user_id'];
    $recentConvos = array();

    //Getting ids of all the users where messages are received from
    $senderOfRecentMsgs = queryFunc("SELECT id,user_from,user_to,deleted FROM messages where ((user_to = $userLoggedIn OR user_from = $userLoggedIn)) AND deleted not like ' $userLoggedIn%' AND deleted not like '%$userLoggedIn ' ORDER BY id DESC ");

    // For keeping track of last message
    $flag = 0;
    if (isData($senderOfRecentMsgs)) {
        while ($row = isRecord($senderOfRecentMsgs)) {
            if ($flag == 0) {
                $_SESSION['last_message_retrieved_for_recent_convos'] = $row['id'];
                $flag = 1;
            }

            //if user logged in is the sender then store reciever's id, else store sender's id
            $idToPush = ($row['user_from'] == $_SESSION['user_id'] ? $row['user_to'] : $row['user_from']);
            //Check whether that sender is already in the list, if not, only then push his id
            // So the array will only have unique IDs
            // Pushing person into the list of recent chats area
            if (array_search($idToPush, $recentConvos) === false) {
                array_push($recentConvos, $idToPush);
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
    $user_name = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name FROM users WHERE user_id=$user_id");
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
    // Getting last message of the conversation
    $userLoggedIn = $_SESSION['user_id'];
    $details = queryFunc("SELECT user_from,user_to,body,dateTime,opened from messages where ((user_to = $partnerId AND user_from = $userLoggedIn) OR (user_to = $userLoggedIn AND user_from = $partnerId)) AND (deleted not like ' $userLoggedIn%' AND deleted not like '%$userLoggedIn ') order by id desc limit 1");
    $details = isRecord($details);
    // Only displaying first 15 characters of message
    if (strlen($details['body']) > 25) {
        $details['body'] = (substr($details['body'], 0, 15) . "..."); // 'Look what we h...'
    }
    return $details;
}

function showRecentChats($place = 0)
{
    // Showing Recents Chats

    // place : 1 if message dropdown

    // Getting recent user IDs
    $recentUserIds = getRecentChatsUserIds();

    if ($recentUserIds) {

        $chatDeleteButton = '';
        $messageSeeMore = '';

        if ($place == 1) {
          
            $messageSeeMore = <<<DATA
            <a href="messages.php" class='see-more'>
              <span>See more</span>
            </a>
DATA;
        }

        $recentUsernames = getRecentChatsUsernames($recentUserIds); // Names of users
        $recentProfilePics = getProfilePicData($recentUserIds); // Pics of users
        $counter = 0;
        while ($counter < sizeof($recentUsernames)) {
            $lastMessageDetails = getPartnersLastMessage($recentUserIds[$counter]);
            $from = $lastMessageDetails['user_from'];

            if ($from == $_SESSION['user_id']) {
                // If last message was sent by you
                $from = "You : ";
            } else {
                $from = '';
            }

            if ($lastMessageDetails['opened'] == 0 && $lastMessageDetails['user_to'] == $_SESSION['user_id']) {
                $noSeen = 'noSeen';
            } else {
                $noSeen = '';
            }

            if ($place != 1) {
                // Not dropdown
                $chatDeleteButton = <<<DATA
                <span class='chat-del-button'  >
                <i class='tooltip-container fas fa-times chat-delete' onclick='javascript:deleteConvo({$recentUserIds[$counter]})'><span class='tooltip tooltip-left'>Delete</span></i>
                 </span>
DATA;
            }

            $msg = $lastMessageDetails['body']; // Message Body
            $at = getTime($lastMessageDetails['dateTime']); // Message Time

            $user = <<<DELIMETER
            <div class='recent-user-div recent-user-{$recentUserIds[$counter]} {$noSeen}'>
            <a href='messages.php?id={$recentUserIds[$counter]}' class='recent-user'>

                <span class='recent-user-image'>
                    <img src='{$recentProfilePics[$counter]}' class='post-avatar post-avatar-40' />
                </span>
                <span class='recent-message-info'>
                    <span class="recent-username">{$recentUsernames[$counter]}</span>
                    <span class='recent-message-text'>{$from}{$msg}</span>
                </span>
                <span class='recent-message-time'>
                    <span>{$at}</span>
                </span>
               
            </a>
            <span>
            $chatDeleteButton
        </span>
            
            </div>
DELIMETER;
            echo $user;
            $counter++; // increment for rendering next recent chat
        }

        // See more button will be rendered if there are more chats
        echo $messageSeeMore;

    } else {
        if ($place == 1) {
            $noData = '<h3>No Messages</h3>';
            echo $noData;
        }
        $_SESSION['last_message_retrieved_for_recent_convos'] = 0;
    }

}

function searchUsersFortChats()
{
    $search = <<<DELIMETER
    <div class="search-message">
    <form action="" method="get" name="message_search_form">

    <input type="text"  onkeyup="getUsers(this.value,0)" name="q" placeholder="Search" autocomplete = "off" id="message_search_text_input" class='search-message-input'>


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
        // If search input field is empty
        echo " ";
    } else {
        $value = strtolower($value);
        //explode breakes the string into array, each substring is made when the first arg of explode is found in the string
        $names = explode(" ", $value);
        if (count($names) == 2) {
            //if there there are two substrings then it would search for first substirng in first name and second string in the last name

            if ($flag == 2) {
                // allSearchResults.php - displaying all results
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,user_id from users where lower(first_name) like '$names[0]%' AND lower(last_name) like '$names[1]%'");
            } else {
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,user_id from users where lower(first_name) like '$names[0]%' AND lower(last_name) like '$names[1]%' limit 5");
            }
        } else {
            //if there is only one substring, i.e no spaces are present in the input then it would search that substring in both first name and last name
            if ($flag == 2) {
                // allSearchResults.php - displaying all results
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,user_id from users where lower(first_name) like '$names[0]%' OR lower(last_name) like '$names[0]%'");
            } else {
                $users = queryFunc("SELECT CONCAT(first_name,' ',last_name) as name,profile_pic,user_id from users where lower(first_name) like '$names[0]%' OR lower(last_name) like '$names[0]%' limit 5");
            }
        }

        // If any user is found against search
        if (isData($users)) {
            if ($flag == 1 || $flag == 2) {
                while ($row = isRecord($users)) {
                    $row['profile_pic'] = "./assets/profile_pictures/" . $row['profile_pic'];
                    $user = <<<DELIMETER
                <div class='search-person-container'>
                    <a href='timeline.php?visitingUserID={$row['user_id']}'  class='search-person'>
                        <span class='search-person-image'>
                <img src='{$row['profile_pic']}' class='post-avatar post-avatar-30'/>
                </span>
                <span class='search-person-info'>
                <span class='person-name'>{$row['name']}</span>
                </span>
                </a>
DELIMETER;
                    // Not displaying message icon if loggedIn user appear in search results
                    if ($row['user_id'] != $_SESSION['user_id']) {
                        $user .= <<<DELIMETER
                    <div class='person-message'>
                    <a href='messages.php?id={$row['user_id']}'><i class='fas fa-envelope message-icon'></i></a>
                    </div>
                </div> 
DELIMETER;
                    } else {
                        // Ending the opened div
                        $user .= '</div>'; // search-person
                    }
                    // Passing the response back
                    echo $user;
                }
            } else {
                // When search is done from messages
                while ($row = isRecord($users)) {
                    $row['profile_pic'] = "./assets/profile_pictures/" . $row['profile_pic'];
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
                    //Don't show user logged in, in search results for chats
                    if ($row['user_id'] != $_SESSION['user_id']) {
                        echo $user;
                    }

                }
            }
        } else {
            // If no user was found against the search
            echo 'No';
        }
    }
}

function getRecentConvo()
{
    //Will get the ID of the person whom you recently had a chat with
    // For opening recent chatted user when displaying messages.php

    $userLoggedIn = $_SESSION['user_id'];
    $recentUser = queryFunc("SELECT user_to,user_from from messages where (user_to = '$userLoggedIn' OR user_from = '$userLoggedIn') AND (deleted not like ' $userLoggedIn%' AND deleted not like '%$userLoggedIn ') order by id DESC limit 1");

    if (isData($recentUser)) {
        $recentUser = isRecord($recentUser);
        $recentPartnerId = ($recentUser['user_from'] == $userLoggedIn) ? $recentUser['user_to'] : $recentUser['user_from'];
        redirection("messages.php?id=$recentPartnerId");
    }
}

function deleteConvo($partnerId)
{
    // Deleting User Chat

    $userLoggedIn = $_SESSION['user_id'];
    $userLoggedInAppendedSpace = $userLoggedIn . " ";

    //Deleting messages from one side that is deleted by loggedIn user
    queryFunc("UPDATE messages set deleted = CONCAT(deleted, '$userLoggedInAppendedSpace') where ((user_to = '$partnerId' AND user_from = '$userLoggedIn') OR (user_to = '$userLoggedIn' AND user_from = '$partnerId')) AND deleted not like '%$userLoggedIn%' ");
}

function coverArea($id)
{
    $queryResult = queryFunc("SELECT * FROM users WHERE user_id='$id'");
    $queryUser = isRecord($queryResult);
    $name = $queryUser['first_name'] . ' ' . $queryUser['last_name'];
    $coverPic = "./assets/cover_pictures/" .$queryUser['cover_pic'];
    $messagesLink = '';
    $notificationsLink = '';

    // Checking if user has set up a cover image?
    if ($coverPic != null) {
        $coverStyle = <<<DATA
        background-image : url({$coverPic})
DATA;
    } else {
        $coverStyle = '';
    }

    // Enabling edit cover and edit profile pic option if it is your profile
    if ($id == $_SESSION['user_id']) {

         

        $messagesLink =<<<DATA
            
                <a href='messages.php' class='messages-link-button'>Messages</a>
            
DATA;

         $notificationsLink =<<<DATA
         <a href='allNotification.php' class='notifications-link-button'>Notifications</a>
DATA;

        $editCover = <<<COVER

        <form onchange='return editCoverPicture()' class="edit-cover-pic hidden" onmouseover ="showEditImageButton('edit-cover-pic')" onmouseout ="hideEditImageButton('edit-cover-pic')">
        <div class='upload-btn-wrapper'>
        <button class='pic-upload-btn'><i class='far fa-image'></i></button>
        <input type='file' name='cover-pic'/>
        <span class='cover-pic-name'></span>
        </div>
        </form>

COVER;

        $editProfilePic = <<<PROFILE
        <form onchange="return editProfilePicture()" method="post" class="edit-profile-pic hidden" onmouseover ="showEditImageButton('edit-profile-pic')" onmouseout ="hideEditImageButton('edit-profile-pic')">
                <div class='upload-btn-wrapper'>
                <button class='pic-upload-btn'><i class='far fa-image'></i></button>
                <input type='file' name='profile-pic'/>
                <span class='profile-pic-name'></span>
            </div>
            </form>

PROFILE;
    } else {
        $editCover = '';
        $editProfilePic = '';
    }
    $queryUser['profile_pic'] = "./assets/profile_pictures/" . $queryUser['profile_pic'];
    $content = <<<PROFILE
    <div class='user-cover' onmouseover ="showEditImageButton('edit-cover-pic')" onmouseout ="hideEditImageButton('edit-cover-pic')" style='$coverStyle' >
        $editCover
        <div class='user-pic'>
            <span class='user-pic-container' onmouseover ="showEditImageButton('edit-profile-pic')" onmouseout ="hideEditImageButton('edit-profile-pic')">
            <img src='{$queryUser['profile_pic']}' onclick="showImage()"  id="profile_picture"/>
            </span>
            $editProfilePic
        </div>
PROFILE;
    if (isFriend($id) || $_SESSION['user_id'] == $id) {
        // Enabling seeing of recent activities and friends list if visited user is your friend
        $content .= <<<PROFILE
    <div id="modal" class="modal">
            <span class="close" id="modal-close" onclick="onClosedImagModal()">&times;</span>
            <img class="modal-content" id="modal-img" src="">
        </div>
PROFILE;

        
        $friendsLink = "requests.php";
        $activitiesLink = "allActivities.php";
        
        if (isFriend($id)) {
            // Will route to the visited user friends and activity page
            $friendsLink = $friendsLink . "?id=" . $id;
            $activitiesLink = $activitiesLink . "?id=" . $id;
            
        }
        $content .= <<<PROFILE
    </div>
    <div class='user-timeline-tabs'>

    <div class='friends-link'>
        <a href='{$friendsLink}' class='friends-link-button'>Friends</a>
        {$messagesLink}
    </div>

        

        <div class='user-info'>
        <h3>{$name}</h3>
        <span>{$queryUser['email']}</span>
        </div>

        <div class='newsfeed-link'>
        $notificationsLink
        <a href='{$activitiesLink}' class='recent-activities-link-button'>Recent Activities</a>
        
    </div>
    </div>

PROFILE;
    } else {

        $content .= <<<PROFILE
        </div>
        <div class='user-timeline-tabs'>
            <div class='user-info'>
                <h3>{$name}</h3>
                <span>{$queryUser['email']}</span>
            </div>
        </div>

PROFILE;
    }
    echo $content;
}

// These two functions are of no use but keep them
function turnOnline($id)
{
    queryFunc("UPDATE users set online = 1 where user_id =$id");
    queryFunc("UPDATE users set active_ago=0 WHERE user_id={$id}");
    $_SESSION['last_msg_id'] = 0;
}

function turnOffline($id)
{
    queryFunc("update users set online = 0 where user_id =" . $id);
    queryFunc("UPDATE users set active_ago=now() WHERE user_id={$id}");
}

function activeAgo($id)
{
    $queryResult = queryfunc("SELECT active_ago from users WHERE user_id={$id}");
    $timeResult = isRecord($queryResult);

    $time = getTime($timeResult['active_ago']);

    return $time;
}

function showRecentActivities($page, $limit, $place = null, $id = null)
{
    // Show recent activities

    // activity_type == 0 ==> Like
    // activity_type == 1 ==> Comment
    // activity_type == 2 ==> Post
    // activity_type == 3 ==> Added Friend
    // activity_type == 4 ==> Unlike

    // limit - number of activites to render

    // place
    // 1 - main.php area
    // 2 - recent activities page

    //if id is true then you are seeing someone else's activities
    $userLoggedIn = $_SESSION['user_id'];
    if ($id != null) {
        $userLoggedIn = $id;
    }

    $limitRecords = $limit + 1;

    if ($place == 1) {
        $activities = queryFunc("SELECT * from recent_activities where user_id = '$userLoggedIn' order by activity_id desc limit $limitRecords");
    } elseif ($place == 2) {
        $activities = queryFunc("SELECT * from recent_activities where user_id = '$userLoggedIn' order by activity_id desc");
    }

    if ($page == 1) { // if you are at first page then starting with activity 0
        $start = 0;
    } else {
        // else calculating which activity to start from
        $start = ($page - 1) * $limit;
    }
    $numberOfIteration = 0; // //Number of results checked - once it reaches to value of start we start rendering activity.

    // Initial value of this variable
    $_SESSION['more_activities'] = 3;

    if (isData($activities)) {
        $count = 1; // To keep track of no of activities rendered

        while ($row = isRecord($activities)) {
            //Wait to reach start value to start rendering activities, because before $start are already rendered

            //If defined number of activities are rendered then break
            //once it reaches to value of $start we start rendering activities.
            if ($numberOfIteration++ < $start) {
                continue;
            }
            if ($count > $limit) {
                // Limit has been reached - shift to next page now xD
                $_SESSION['more_activities'] = 1;
                break;
            } else {
                $count++;
            }
            addActivity($row['activity_type'], $row['activity_at_id'], $row['user_id'], $id);
        }
        // If it is a recent activity page
        if ($place == 2) {
            // If limit was reached
            if ($count > $limit) {
                $infoForNextTime = "<input type='hidden' id='noMoreActivities' value='false'><input type='hidden' id='nextPageActivities' value='" . ($page + 1) . "' >";
            } else {
                $infoForNextTime = "<input type='hidden' id='noMoreActivities' value='true'>";
            }
            echo $infoForNextTime;
        }
    }
    // Displaying message depending on variable value
    if ($_SESSION['more_activities'] == 3) { // If limit was not reached in rendering activities
        if ($numberOfIteration == 0) { // If no activity was there to render
            $_SESSION['more_activities'] = 0;
        } else {
            $_SESSION['more_activities'] = 2; // if some activities were rendered
        }
    }
}

function addActivity($activity_type, $target_id, $userLoggedIn, $id = null)
{
    // Activity type
    // activity_type == 0 ==> Like
    // activity_type == 1 ==> Comment
    // activity_type == 2 ==> Post
    // activity_type == 3 ==> Added Friend
    // activity_type == 4 ==> Unlike

    //if id is null then user is visiting his own timeline
    //else he is visiting someone else's

    //Target_id
    // target - content such as post,like etc
    $userLoggedIn = $_SESSION['user_id'];

    //flag2 is used for checking whether user logged is authorized for viewing that post
    $flag2 = false;
    if ($id) {
        $userLoggedIn = $id;
        if ($activity_type != 3) {
            $friend = queryFunc("SELECT user_id from posts where post_id = '$target_id'");
            if (isData($friend)) {
                $friend = isRecord($friend);
                $friend = $friend['user_id'];
                if (!(isFriend($friend)) && $friend != $_SESSION['user_id']) {
                    $flag2 = true;
                }

            } else {
                $friend = null;
            }

        }

    }
    $profilePic = getUserProfilePic($userLoggedIn);
    $deletedActivity = '';

    // To keep track of activity,whether it was deleted or not?
    $flag = true;

    if ($activity_type == 0) {
        // If like activity
        $conflict = 'liked a post';
        $activityIcon = 'far fa-thumbs-up';
        $activityLink = "notification.php?postID={$target_id}&type=liked&notiID=0";
        $time = queryFunc("SELECT createdAt from likes where post_id = $target_id and user_id = $userLoggedIn");

        // is activity deleted?
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['createdAt'];
        } else {
            $flag = false;
        }
    } elseif ($activity_type == 1) {

        // If comment activity
        $conflict = 'commented on a post';
        $activityIcon = 'far fa-comment-dots';

        // targetID will have two IDS i.e postID and comment ID
        $commentDetails = explode(" ", $target_id);
        $activityLink = "notification.php?postID=$commentDetails[0]&type=commented&notiID=0";
        $time = queryFunc("SELECT createdAt from comments where comment_id = '$commentDetails[1]'");

        // is activity deleted?
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['createdAt'];
        } else {
            $flag = false;
        }

    } elseif ($activity_type == 2) {
        // If post activity
        $conflict = 'added a post';
        $activityIcon = 'fas fa-pencil-alt';
        $activityLink = "notification.php?postID=$target_id&type=post&notiID=0";
        $time = queryFunc("SELECT createdAt from posts where post_id = $target_id");

        // is activity deleted?
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['createdAt'];
        } else {
            $flag = false;
        }

    } elseif ($activity_type == 3) {

        // If friend Activity
        $conflict = 'made a new friend';
        $activityIcon = 'fas fa-user-plus';

        // Will have IDs of two users
        $users = explode(" ", $target_id);
        $visitId = ($users[0] == $userLoggedIn) ? $users[1] : $users[0];
        $activityLink = "timeline.php?visitingUserID=$visitId";
        $time = queryFunc("SELECT become_friends_at from friends where (user1 = '$users[0]' AND user2 = '$users[1]') OR (user1 = '$users[1]' AND user2 = '$users[0]') ");

        // is activity deleted?
        if (isData($time)) {
            $time = isRecord($time);
            $time = $time['become_friends_at'];
        } else {
            $flag = false;
        }
    }
    if ($flag && !$flag2) {
        // Activity not deleted, so getting its time
        $time = getTime($time);
    } else {
        // Activity deleted
        if (!($flag)) {
            $time = "Deleted";
        }
        //Unauthorized bcoz flag2 is true
        else {
            $time = "Unauthorized Access";
        }
        $deletedActivity = 'deleted-activity';
        $activityLink = "javascript:void(0)";
    }
    if ($id && $id != $_SESSION['user_id']) {
        // if it is not your activity
        $user = queryFunc("SELECT first_name from users where user_id = '$id'");
        $user = isRecord($user);
        $user = $user['first_name'];
    } else {
        $user = "You";
    }

    $noti = <<<NOTI
        <a href={$activityLink} class='recent-activity recent_activity {$deletedActivity}'>
            <span class='recent-activity-image'>
                <img src='{$profilePic}' class='post-avatar post-avatar-30' />
            </span>
            <span class='recent-activity-info'>
                <span class='recent-activity-text'>{$user} {$conflict}</span><i class='recent-activity-icon {$activityIcon}'></i><span class='recent-activity-time {$deletedActivity}'>{$time}</span>
            </span>
        </a>
NOTI;
    echo $noti;
}

function CountDropdown($place)
{

    // For displaying number of unseen notifications
    // place
    // 1 -> Notification
    // 2 -> Messages
    // 3 -> Requests

    $userID = $_SESSION['user_id'];

    if ($place == 1) {
        $queryResult = queryFunc("SELECT count(*) as count from notifications WHERE d_user_id='$userID' AND seen=0");
    } elseif ($place == 2) {
        $queryResult = queryFunc("SELECT count(DISTINCT user_from) as count FROM messages WHERE user_to=$userID and opened = 0");

    } elseif ($place == 3) {
        $queryResult = queryFunc("SELECT count(*) as count FROM friend_requests WHERE to_id ='$userID' and status = 0");
    }

    $count = isRecord($queryResult);
    $countValue = $count['count'];

    return $countValue;
}

function countDropdownDisplay($value, $place)
{
    // value - number of count to be displayed
    // place - which dropdown?

    if ($value == 0) {
        echo "<script>document.querySelector('.$place-count').style.backgroundColor='transparent';</script>";
        
    } else {
        echo "<script>document.querySelector('.$place-count').style.backgroundColor='red';</script>";
        echo $value;
    }
}

function coverPicChange($pic)
{

    $userID = $_SESSION['user_id'];
    $queryCoverPic = queryFunc("UPDATE users set cover_pic='$pic' where user_id=$userID");
}

function profilePicChange($pic)
{

    $userID = $_SESSION['user_id'];
    $queryCoverPic = queryFunc("UPDATE users set profile_pic='$pic' where user_id=$userID");
}

function showUserInfo($id)
{
    $userInfo = queryFunc("SELECT age as 'actualAge',gender,school,college,university,contact_no,work, question,answer, TIMESTAMPDIFF(YEAR, age, now()) as 'age' from users where user_id = '$id'");
    if (isData($userInfo)) {
        $userInfo = isRecord($userInfo);
        
        $defaultValue = "-";
        //Setting default value if there is no value
        if (!isset($userInfo['school']) || strlen($userInfo['school']) == 0) {
            $userInfo['school'] = $defaultValue;
        }

        if (!isset($userInfo['college']) || strlen($userInfo['college']) == 0) {
            $userInfo['college'] = $defaultValue;
        }

        if (!isset($userInfo['university']) || strlen($userInfo['university']) == 0) {
            $userInfo['university'] = $defaultValue;
        }

        if (!isset($userInfo['work']) || strlen($userInfo['work']) == 0) {
            $userInfo['work'] = $defaultValue;
        }

        if (!isset($userInfo['contact_no']) || strlen($userInfo['contact_no']) == 0) {
            $userInfo['contact_no'] = $defaultValue;
        }
        if (!isset($userInfo['question']) || strlen($userInfo['question']) == 0) {
            $userInfo['question'] = $defaultValue;
        }

        if (!isset($userInfo['age']) || strlen($userInfo['age']) == 0) {
            $userInfo['age'] = $defaultValue;
        } else {
            $userInfo['age'] .= " Years";
        }

        $infoValues = array('school' => $userInfo['school'], 'college' => $userInfo['college'] ,'university' => $userInfo['university'],'work' => $userInfo['work'],'contact' => $userInfo['contact_no'], 'age' => $userInfo['age'], 'gender' => $userInfo['gender']);

        $info = '';


        foreach ($infoValues as $key => $value) {
            $info .=<<< INFO
            <div class='user-info user-info-{$key}'>
                <div class='user-info-key'>{$key}</div>
                <div class='user-info-value user-{$key}'>{$value}</div>
                <!-- <i class='fas fa-edit' onclick='javascript:userInfoEditField("$key","$value")'></i> -->
            </div>
INFO;
        }

        $info .= <<<INFO
        <input type = 'hidden' class = "actualAge" value = "{$userInfo['actualAge']}">
INFO;

        if (isset($_SESSION['edit_info_pass_error']) && $_SESSION['edit_info_pass_error']) {
            $showHidden = "";
            // unset($_SESSION['edit_info_pass_error']);
        } else {
            $showHidden = "hidden";
        }
        if ($id == $_SESSION['user_id']) {
            $info .= "<div class='user-info'>
                        <div class='user-info-key'>Security Question:</div>
                        <div class='user-info-value user-question'>{$userInfo['question']} </div>
                      </div>
                      <div class='user-info-edit-container'>
                      <button class='user-info-edit-button' id = 'edit-form' onclick = 'showEditInfoDiv()'>Edit</button></div>";

        }?>
        <div class = "user-info-edit-div-container <?php echo $showHidden; ?>">    
        <div class="user-info-edit-div">
           <h1 class = "user-info-edit-div-heading">Edit Personal Information</h1>
            <span class="user-info-edit-div-close" onclick="hideEditInfoDiv()">&times;</span>

            <hr>

            <div class = "user-info-edit-div-content">
                <form action = "" method = "post" id = "editForm">

        <div class='user-info-display'>
                    <h3 class='user-info-display-heading'>Academic</h3>

                     <input placeholder='School' type = "text" name = "school" class ="user-edit-field user-edit-school" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_school']) ? $_SESSION['edit_info_user_school'] : '';
            echo $val;?>">

                    <input placeholder='College' type = "text" name = "college" class = "user-edit-field user-edit-college" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_college']) ? $_SESSION['edit_info_user_college'] : '';
            echo $val;?>" >
                    <input placeholder='University' type = "text" name = "university" class = "user-edit-field user-edit-university" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_university']) ? $_SESSION['edit_info_user_university'] : '';
            echo $val;?>">

        </div>

        <div class='user-info-display'>
                    <h3 class='user-info-display-heading'>Contact</h3>

                   <input placeholder='Work' type = "text" name = "work" class = "user-edit-field user-edit-work" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_work']) ? $_SESSION['edit_info_user_work'] : '';
            echo $val;?>">
                    <input placeholder='Contact' type = "text" name = "contact" class = "user-edit-field user-edit-contact" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_contact']) ? $_SESSION['edit_info_user_contact'] : '';
            echo $val;?>">

</div>

    <div class='user-info-display'>
                <h3 class='user-info-display-heading'>Personal</h3>



                    <input placeholder='Age' type = "date" name = "age" class ="user-edit-field user-edit-age"  autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_age']) ? $_SESSION['edit_info_user_age'] : '';
            echo $val;?>">
            <span class='edit-error-msg'></span>

                    <select placeholder='Gender' value='Male' name="genderBox"  required class='user-edit-field user-edit-gender'>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                   
                    
                    <input placeholder='Security Question' type = "text" name = "question" class = "user-edit-field user-edit-question" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_question']) ? $_SESSION['edit_info_user_question'] : '';
            echo $val;?>">
                    <input placeholder='Answer' type = "text" name = "answer" class = "user-edit-field user-edit-answer" autocomplete="off" value = "<?php $val = isset($_SESSION['edit_info_user_answer']) ? $_SESSION['edit_info_user_answer'] : '';
            echo $val;?>">

        </div>

        <div class='user-info-display'>


                    <h3 class='user-info-display-heading' >Change Password</h3>
                    <input type = "password" name = "newPassword" class = "user-edit-field user-edit-new-password" autocomplete="off" placeholder="Password">
                    <input type = "password" name = "rePass" class = "user-edit-field user-edit-new-repeat-password" autocomplete="off" placeholder="Confirm Password">
        </div>

        <div class='user-info-display'>
                    <h3 class='user-info-display-heading'>Save Changes</h3>
                    <input placeholder='Current Password'  type = "password" name = "password" class = "user-edit-field user-edit-old-password" autocomplete="off">
        </div>
                    <div class='user-edit-save-container'>
                    <input type = "button" value = "Save" name="save" class = "user-edit-save" onclick = "submitEditInfoForm()">
    </div>  
                </form>
            </div>
        <?php
if (isset($_SESSION['edit_info_pass_error']) && $_SESSION['edit_info_pass_error']) {
                ?>
                <div class='user-info-wrong-pass-warning'>Wrong Password</div>
                <?php
unset($_SESSION['edit_info_pass_error']);
            }
            ?>
        </div>
        </div>
        <?php
}
        echo $info;
    }


function saveEditedInfo($school, $college, $university, $work, $contact, $newPass, $age, $gender,$question,$answer)
{
    $userLoggedIn = $_SESSION['user_id'];
    $conflict1 = "";
    $conflict2 = "";
    if (strlen(trim($newPass)) > 8) {
        $conflict1 = ", password = '$newPass' ";
    }
    if(!empty($answer)){
        $answer = hashString($answer);
        $conflict2 = ", answer = '$answer' ";
    }
    
    $result = queryFunc("UPDATE users set school = '$school' , college = '$college' , university = '$university', work = '$work' , contact_no = '$contact' $conflict1  $conflict2 , age = '$age' , gender = '$gender', question = '$question' where user_id = '$userLoggedIn' ");
    // isData($result);
    
}

function validatePassword($pass)
{
    $userLoggedIn = $_SESSION['user_id'];
    $id = queryFunc("SELECT user_id from users where password = '$pass' and user_id = '$userLoggedIn'");
    if (isData($id)) {
        return true;
    } else {
        return false;
    }

}

function showPeopleYouMayKnow()
{

    //To keep recored of no of iterataion when record is actually rendered
    $numberOfSuccessfulIteration = 0;

    //Getting latest user_id to generate maximum value of ID from rand and to end loop as well
    $maxID = queryFunc("SELECT user_id from users order by user_id DESC limit 1");
    $maxID = isRecord($maxID);
    $maxID = $maxID['user_id'];

    //Two arrays, one to store IDs to user already checked and 2nd to store IDs of people who are rendered on the page, to avoid repition
    $renderedIDs = array();
    $checkedIDs = array();

    while (true) {
        //Generate a random number
        $idToCheck = rand(1, $maxID);

        //If not already in checked then add it
        if (!in_array($idToCheck, $checkedIDs)) {
            array_push($checkedIDs, $idToCheck);
        }

        //If the user is your friend, or you have sent him req or vice versa  or already rendered on the page, then begin from top again
        if (isFriend($idToCheck) || reqSent($idToCheck) || reqRecieved($idToCheck) || $idToCheck == $_SESSION['user_id'] || in_array($idToCheck, $renderedIDs)) {
            continue;
        }

        //else fetch that user from DB and render it after checking that this ID belongs to a legit user
        $person = queryFunc("SELECT profile_pic, CONCAT(first_name ,' ', last_name) as 'name' from users where user_id = '$idToCheck'");
        if (isData($person)) {
            array_push($renderedIDs, $idToCheck);
            $person = isRecord($person);
            $time = activeAgo($idToCheck);
            $person['profile_pic'] = "./assets/profile_pictures/" . $person['profile_pic'];
            $stateClass = 'state-off';
            if ($time == 'Just Now') {
                $time = 'Now';
                $stateClass = 'state-on';
            }
            $content = <<<USER

                    <div class='people-you-may-know'>
                        <div class='people-you-may-know-image'>
                            <img class='post-avatar post-avatar-30' src='{$person['profile_pic']}'  >
                        </div>
                        <div class='people-you-may-know-info'>
                            <a href="timeline.php?visitingUserID={$idToCheck}" class='people-you-may-know-text'>{$person['name']}</a>
                            <span class='{$stateClass}'>{$time}</span>
                        </div>
                        <div class='people-you-may-know-action'>
                            <div>
                                <a href="javascript:addFriend({$idToCheck})" class='add-friend add-friend-{$idToCheck}'><i class="tooltip-container fas fa-plus">
                                <span class='tooltip tooltip-right'>Add Friend</span></i></a>
                            </div>
                        </div>
                    </div>

USER;
            echo $content;

            if (++$numberOfSuccessfulIteration > 11) {
                break;
            }

        }
        if (sizeof($checkedIDs) >= $maxID) {
            break;
        }

    }
}

function showUserActivitiesSummary($id)
{
    $noOfPosts = queryFunc("SELECT count(*) as 'posts' from posts where user_id = '$id'");
    $noOfPosts = isRecord($noOfPosts);
    $noOfPosts = $noOfPosts['posts'];

    $noOfLikes = queryFunc("SELECT count(*) as 'likes' from likes where user_id = '$id'");
    $noOfLikes = isRecord($noOfLikes);
    $noOfLikes = $noOfLikes['likes'];

    $noOfComments = queryFunc("SELECT count(*) as 'comments' from comments where user_id = '$id'");
    $noOfComments = isRecord($noOfComments);
    $noOfComments = $noOfComments['comments'];

    $noOfFriends = queryFunc("SELECT count(*) as 'friends' from friends where user1 = '$id' OR user2 = '$id' ");
    $noOfFriends = isRecord($noOfFriends);
    $noOfFriends = $noOfFriends['friends'];



    // $stats = array('Posts' => $noOfPosts, 'Likes' => $noOfLikes, 'Comments' => $noOfComments, 'Friends' => $noOfFriends);
    $stats  = array();
    if($noOfPosts > 1){
        $stats['Posts'] = $noOfPosts;
    }else{
        $stats['Post'] = $noOfPosts;
    }

    if($noOfLikes > 1){
        $stats['Likes'] = $noOfLikes;
    }else{
        $stats['Like'] = $noOfLikes;
    }

    if($noOfComments > 1){
        $stats['Comments'] = $noOfComments;
    }else{
        $stats['Comment'] = $noOfComments;
    }

    if($noOfFriends > 1){
        $stats['Friends'] = $noOfFriends;
    }else{
        $stats['Friend'] = $noOfFriends;
    }


    $content = '';

    foreach ($stats as $stat => $value) {
        $conflict = $stat == "Posts" ? "first-stat" : "";
        $content .= <<<STATS
        <div class='stat {$conflict}'>
            <div class='stat-value'>{$value}</div>
            <div class='stat-heading'>{$stat}</div>
        </div>
STATS;
    }
    echo $content;
}

function getUploadedPics($userID){
    $posts = queryFunc("SELECT post_id,pic from posts where pic != '' AND user_id = '$userID' order by post_id desc limit 10");
    $mediaFlag = 0;
    

    if(isData($posts)){
        while($post = isRecord($posts)){


            $picOrVideo = explode(".",$post['pic']);
            $mediaFlag = 0;
            $postMedia = '';

            if(isset($picOrVideo[1])){
                $picOrVideo = $picOrVideo[1];
                if($picOrVideo == "mp4" || $picOrVideo == "flv" || $picOrVideo == "avi"){
                    $postPic = "./assets/post_videos/" . $post['pic'];
                    $mediaFlag = 1;
                }else{
                    $postPic = "./assets/post_pics/" . $post['pic'];
                    $mediaFlag = 2;
                }  
            }else{
                $postPic = "./assets/post_pics/" . $post['pic'];
            }


            if($mediaFlag == 1){
                $postMedia = <<<DATA
                <video class='recent-upload-video'>
                    <source src="{$postPic}" >
                </video>
DATA;
            }elseif($mediaFlag == 2){
                $postMedia = <<<DATA
                <img src='{$postPic}' class='recent-upload-image'>
DATA;
            }
            
            $content = <<<PIC
                <a href = "./notification.php?postID={$post['post_id']}&type=''&notiID=''" class="recent-uploads">
                    $postMedia
                </a>    
PIC;
        echo $content;                
        }
    }
    else{
        $_SESSION['recent_uploads'] = 0;
    }
}

function clearString($string){
    global $connection;
    return mysqli_real_escape_string($connection,(htmlentities(stripslashes(trim($string)))));
}

function checkAttempts($email){
    $data = queryFunc("SELECT attempts,wrong_answer_time from users where email = '$email'");   
    $data = isRecord($data);
    if($data['attempts'] < 3)
        echo "yes";
    else{
        $time = differenceInTime($data['wrong_answer_time']);
        if($data['attempts'] == 3){
            updateWrongAttempts($email);
            queryFunc("update users set wrong_answer_time = now() where email = '$email'"); 
            $time = getWrongAttemptTime($email);
            echo $time;   
        }
        else if($time > 3599){
            echo "yes";
            updateWrongAttempts($email,0); 
        }
        else{
            $time = getWrongAttemptTime($email);
            echo $time;
        }
    }    
}

function updateWrongAttempts($email,$count=null){
    if(isset($count))
        $attempts = 0;
    else 
        $attempts = "attempts + 1";
    queryFunc("update users set attempts = $attempts where email = '$email'");
}

function getWrongAttemptTime($email){
    $time = queryFunc("SELECT wrong_answer_time from users where email = '$email'");
    $time = isRecord($time);
    return (differenceInTime($time['wrong_answer_time']));
}

function sendReqFromDefaultAccount($id){
    $defaultAccountId = 2;
    $friend = queryFunc("INSERT INTO friend_requests (to_id, from_id) values({$id},{$defaultAccountId})");
    notification($defaultAccountId, $id, 0, 'request');
}

function sideBar(){

    $iconArray = array("newspaper","user","bell","comments","user-friends","chart-line");

    $entity = array("Newsfeed" => "main.php",
                    "Timeline" => "timeline.php",
                    "Notifications" => "allNotification.php",
                    "Messages" => "messages.php",
                    "Friends" => "requests.php",
                    "Activites" => "allActivities.php"    
                );

    $counter = 0;
    $sidebar = "";

    foreach ($entity as $name => $location) {
        $currentIcon = $iconArray[$counter++];
    
        $sidebar .=<<<CONTENT
            <div class='navigation'>
                <a href='{$location}'>
                    <i class='tooltip-container fas fa-{$currentIcon}'>
                        <span class='tooltip tooltip-right'>{$name}</span>
                    </i>
                </a>
            </div>
CONTENT;
    }

    echo $sidebar;
}

//ADMIN FUNCTIONS
function deleteUser($id){
    $check = queryFunc("SELECT user_id from users where user_id = $id");
    if(isData($check)){
        queryFunc("DELETE from comments where user_id = '$id'");
        queryFunc("DELETE from friends where user1 = '$id' OR user2 = '$id'");
        queryFunc("DELETE from friend_requests where from_id = '$id' OR to_id = '$id'");
        queryFunc("DELETE from likes where user_id = '$id'");
        queryFunc("DELETE from messages where user_from = '$id' OR user_to = '$id'");
        queryFunc("DELETE from notifications where s_user_id = '$id' OR d_user_id = '$id'");
        queryFunc("DELETE from posts where user_id = '$id'");
        queryFunc("DELETE from recent_activities where user_id = '$id'");
        queryFunc("DELETE from users where user_id = '$id'");
        echo "Account Removed";
    }
    else{
        echo "User Doesn't Exist";
    }    

}

function showUserActivitiesSummaryForAdmin($id){
    $user = queryFunc("SELECT first_name as username from users where user_id = '$id'");
    if(isData($user)){
        $user = isRecord($user);
        $user = $user['username'];

        $noOfPosts = queryFunc("SELECT count(*) as 'posts' from posts where user_id = '$id'");
        $noOfPosts = isRecord($noOfPosts);
        $noOfPosts = $noOfPosts['posts'];

        $noOfLikes = queryFunc("SELECT count(*) as 'likes' from likes where user_id = '$id'");
        $noOfLikes = isRecord($noOfLikes);
        $noOfLikes = $noOfLikes['likes'];

        $noOfComments = queryFunc("SELECT count(*) as 'comments' from comments where user_id = '$id'");
        $noOfComments = isRecord($noOfComments);
        $noOfComments = $noOfComments['comments'];

        $noOfFriends = queryFunc("SELECT count(*) as 'friends' from friends where user1 = '$id' OR user2 = '$id' ");
        $noOfFriends = isRecord($noOfFriends);
        $noOfFriends = $noOfFriends['friends'];

        $noOfReqRecieved = queryFunc("SELECT count(*) as 'reqRec' from friend_requests where to_id = '$id' and status = 0 ");
        $noOfReqRecieved = isRecord($noOfReqRecieved);
        $noOfReqRecieved = $noOfReqRecieved['reqRec'];

        $noOfReqSent = queryFunc("SELECT count(*) as 'reqSent' from friend_requests where from_id = '$id' and status = 0 ");
        $noOfReqSent = isRecord($noOfReqSent);
        $noOfReqSent = $noOfReqSent['reqSent'];

        $noOfReqCanceled = queryFunc("SELECT count(*) as 'reqCanceled' from friend_requests where from_id = '$id' and status = 2 ");
        $noOfReqCanceled = isRecord($noOfReqCanceled);
        $noOfReqCanceled = $noOfReqCanceled['reqCanceled'];

        $noOfMsgRecieved = queryFunc("SELECT count(*) as 'msgRec' from messages where user_to = '$id'");
        $noOfMsgRecieved = isRecord($noOfMsgRecieved);
        $noOfMsgRecieved = $noOfMsgRecieved['msgRec'];

        $noOfMsgsSent = queryFunc("SELECT count(*) as 'msgSent' from messages where user_from = '$id'");
        $noOfMsgsSent = isRecord($noOfMsgsSent);
        $noOfMsgsSent = $noOfMsgsSent['msgSent'];

        $noOfMsgsDeleted = queryFunc("SELECT count(*) as 'msgDeleted' from messages where (user_from = '$id' OR user_to = '$id') and (deleted like ' $id%' OR deleted like '%$id ')");
        $noOfMsgsDeleted = isRecord($noOfMsgsDeleted);
        $noOfMsgsDeleted = $noOfMsgsDeleted['msgDeleted'];

        $activeAgo = activeAgo($id);

        $stats = array('User Name' => $user, 'Last Online' => $activeAgo ,'Posts' => $noOfPosts, 'Likes' => $noOfLikes, 'Comments' => $noOfComments, 'Friends' => $noOfFriends, 'Requests Sent' => $noOfReqSent , 'Requests Canceled' => $noOfReqCanceled , 'Requests Recieved' =>$noOfReqRecieved , 'Messages Sent' => $noOfMsgsSent , 'Messages Recieved' => $noOfMsgRecieved, 'Messages Deleted' => $noOfMsgsDeleted);

        $content = '';

        foreach ($stats as $stat => $value) {
            $conflict = $stat == "Posts" ? "first-stat" : "";
            $content .= <<<STATS
            <div class='stat {$conflict}'>
                <div class='stat-value'>{$value}</div>
                <div class='stat-heading'>{$stat}</div>
            </div>
STATS;
        }
        echo $content;
    }
    else{
        echo false;
    }    
}

function showLatestRegisteredUsers(){
    $users = queryFunc("SELECT CONCAT(first_name, ' ', last_name) as name, user_id, profile_pic from users order by user_id DESC limit 10");
    while ($row = isRecord($users)) {
        $time = activeAgo($row['user_id']);
        $row['profile_pic'] = "./assets/profile_pictures/" . $row['profile_pic'];
        $stateClass = 'state-off';
        if ($time == 'Just Now') {
            $time = 'Now';
            $stateClass = 'state-on';
        }
        $content = <<<USER
            <div class='latest-user'>
                <div class='latest-user-image'>
                    <img class='post-avatar post-avatar-30' src='{$row['profile_pic']}' >
                </div>
                <div class='latest-user-info'>
                    <a href="timeline.php?visitingUserID={$row['user_id']}" class='latest-user-text'>{$row['name']}</a>
                    <span class='{$stateClass}'>{$time}</span>
                </div>
            </div>
USER;
        echo $content;
    }
}

// ADMIN FUNCTIONS ENDED

function checkUserPosts($id){
    $posts = queryFunc("SELECT count(*) as count from posts where user_id = $id");
    $posts = isRecord($posts);
    if($posts['count'] < 15)
        echo true;
    else
        echo true;    
}

function checkUserComments(){
    $id = $_SESSION['user_id'];
    $comments = queryFunc("SELECT count(*) as count from comments where user_id = $id");
    $comments = isRecord($comments);
    if($comments['count'] < 30)
        echo true;
    else
        echo true;
}

function checkUserMessages(){
    $id = $_SESSION['user_id'];
    $messages = queryFunc("SELECT count(*) as count from messages where user_from = $id");
    $messages = isRecord($messages);
    if($messages['count'] < 50)
        echo true;
    else
        echo true;
}

function checkUserRequests(){
    $noOfReqs = queryFunc("SELECT count(*) as count from friend_requests where from_id = {$_SESSION['user_id']} AND status = 0");
    $noOfReqs = isRecord($noOfReqs);
    $noOfReqs = $noOfReqs['count'];
    if($noOfReqs < 10)
        return true;
    else
        return true;    
}


function updateDropdowns(){
    /*
        $place 
        1 => Notifications
        2 => Messages
        3 => Friend Requests
    */

   showRecentChats(1);
}