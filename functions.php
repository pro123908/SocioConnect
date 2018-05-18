<?php
require('db.php');
session_start();


function set_message($msg){
  if(!empty($msg)){
      $_SESSION['message'] = $msg;
  }
  else{
      $msg = "";
  }
}

function display_message(){
  if(isset($_SESSION['message'])){
      echo $_SESSION['message'];
      unset($_SESSION['message']);
  }
}

function queryFunc($query)
{
    global $connection;
    $queryResult = mysqli_query($connection, $query);

    if (!$queryResult) {
        die('Error in querying database '.mysqli_error($connection));
    }
    return $queryResult;
}

function isData($queryResult)
{
    return (mysqli_num_rows($queryResult) != 0);
}


//faster
function isRecord($queryResult)
{
    return  (mysqli_fetch_assoc($queryResult));
    
}


//Slower
function fetch_array($result){
	return mysqli_fetch_array($result);
}

//Not working
function escape_string($string){
	global $connection;
    return mysqli_real_escape_string($connection,$string);
    
} 

function hashString($string)
{
    $hash = '$2y$10$';
    $salt = 'thisisthestringusedfor';

    $hashed = $hash.$salt;

    $string = crypt($string, $hashed);

    return $string;
}

function redirection($path)
{
    header('Location: '.$path);
}

function add_post(){
    $addPost = <<<DELIMETER
         <div id='addPost'>
            <h2>Add a post</h2>
            <form action="post.php" method='POST'>
                <textarea name="post" id="" cols="50" rows="10" placeholder='Start Writing'></textarea><br><br>
                <input type="file"><br><br>
                <input type="submit" name='submit' value='Post' class='postBtn'>
            </form>
        </div>
DELIMETER;
echo $addPost; 
}

function deletePost($postID){
    $deleteQuery = queryFunc("DELETE from posts WHERE post_id ='$postID'");
    return $deleteQuery;
}

function deleteComment($commentID){
    $deleteQuery = queryFunc("DELETE from comments WHERE comment_id ='$commentID'");
    return $deleteQuery;
}

function addComment($userID,$postID,$comment){
    global $connection;
    $stmt = $connection->prepare("INSERT INTO comments (user_id, post_id, comment,createdAt) VALUES (?, ?, ?,now())");
	$stmt->bind_param("iis",$userID,$postID ,$comment );
	$stmt->execute();
    $stmt->close();
    
    $queryResult = queryFunc("SELECT comment_id from comments ORDER BY comment_id DESC LIMIT 1");
    $row = isRecord($queryResult);

    return $row['comment_id'];
	
}

function show_posts($flag){
    //Selecting all the posts in a manner where user_id matches post_id
    // if flag is true then it is newsfeed else it is your timeline xD
    if($flag)
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id order by post_id desc");
    else
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,' ',last_name) as 'name',createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = {$_SESSION['user_id']} order by post_id desc"); 

    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {
            $postID = $row['post_id'];
            $diffTime = find_difference_of_time($row['createdAt']);
            $timeToShow = create_time_string($diffTime);
            
            //Getting likes count for the current post
            $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
            $likes = isRecord($likesResult);
            
            $post = <<<POST
            <div class='post post_{$postID}'>
                <span class='user'>{$row['name']}</span>
                <span class='postTime'>$timeToShow</span>
                <p class='postContent'>{$row['post']}</p>
                <span class='likeCount likeCount-{$postID}'>{$likes['count']}</span>
                <a class='likeBtn' href='javascript:like({$postID})'>Like</a>
                <a  class='commentBtn' href="javascript:showCommentField({$postID})" >Comment</a>
                <a  class='deleteBtn' href="javascript:deletePost({$postID})" >Delete</a>
            
POST;

            $post .= <<<POST
            <div id="post_id_{$postID}" class='hidden'>
                <div class='commentArea_{$postID}'>

POST;

            $commentResult = queryFunc("SELECT comment_id,comment,CONCAT(first_name,' ',last_name) as 'name',createdAt from comments inner join users on users.user_id = comments.user_id where comments.post_id ='$postID' order by createdAt");
            while ($comments = isRecord($commentResult)) {
                $diffTime = find_difference_of_time($comments['createdAt']);
                $timeToShow = create_time_string($diffTime);
                $commentID = $comments['comment_id'];
                
                
                $post .= <<<POST
                <div class='comment comment_{$commentID}'>
                <a class='commentDelete' href='javascript:deleteComment({$commentID})'>X</a>
                    <span class='commentUser'>{$comments['name']} : </span>
                    <span class='commentText'>{$comments['comment']}</span>
                    <span class='commentTime'>$timeToShow</span>
                </div>
            
POST;
                
            }
            $post .= <<<POST
            </div>
            <div class='commentForm'>
                <form onsubmit="return comment({$postID})" method="post" id='commentForm'>
                    <input name = "comment_{$postID}" type='text'>
                    <input type="text" value="{$postID}" style="display:none" name="post_id_{$postID}">
                    <input type="text" value="{$_SESSION['user']}" style="display:none" name="post_user">
                    <input type='submit' id="{$postID}" value="Comment"> 
                </form>
            </div>
       
    </div>
   </div>
   <br>
POST;
            echo $post;
        }
    }
}


function logout()
{
    session_start();
    session_destroy();
    redirection('index.php');
}

function find_difference_of_time($createdAt){
    $currentTime = queryFunc("SELECT TIMESTAMPDIFF(SECOND, '".$createdAt."', now()) as 'time' ");
    $currentTime = isRecord($currentTime);
    return $currentTime['time'];
}

function create_time_string($timeDate){

    // Time in seconds
    if($timeDate < 60){
        // if it is just one second
        if($timeDate == 1)
            return $timeDate ." Second Ago";
        else
            return $timeDate ." Seconds Ago";
        
    }
    // Time in minutes
    else if($timeDate > 59 && $timeDate < 3600){
        // if it is just one minute
        if(($timeDate / 60) < 2)
            return floor($timeDate / 60) . " Minute Ago";
        else
            return floor($timeDate / 60) . " Minutes Ago"; 
    }
    // Time in hours
    else if($timeDate > 3599 && $timeDate < 86400){
        // Shouldn't it be 3600?
        // if it is just one hour
        if(($timeDate / 3600) < 2)
            return floor($timeDate / 3600) . " Hour Ago";    
        else
            return floor($timeDate / 3600) . " Hours Ago";         
        }
        // Time in days
    else if($timeDate > 86399){
        // if it is just one day
        if(($timeDate / 86400) < 2)
             return floor($timeDate / 86400) . " Day Ago";    
        else
            return floor($timeDate / 86400) . " Days Ago";         
    }
}




?>