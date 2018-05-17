<?php

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

function show_posts($flag){
    //Selecting all the posts in a manner where user_id matches post_id
    if($flag)
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,username,createdAt from posts inner join users on users.user_id = posts.user_id order by post_id desc");
    else
        $queryResult = queryFunc("SELECT post,post_id,posts.user_id,username,createdAt from posts inner join users on users.user_id = posts.user_id where users.user_id = {$_SESSION['user_id']} order by post_id desc"); 
    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {
            $postID = $row['post_id'];
            $diffTime = find_difference_of_time($row['createdAt']);
            $timeToShow = create_time_string($diffTime);
            
            //Getting likes count for the current post
            $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
            $likes = isRecord($likesResult);
            
            $post = <<<POST
            <div class='post'>
                <span class='user'>{$row['username']}</span>
                <span class='post_time'>$timeToShow</span>
                <p>{$row['post']}</p>
                <p class='likeCount-{$postID}'>{$likes['count']}</p>
                <a href='javascript:like({$postID})'>Like</a>
                <a  href="javascript:showCommentField({$postID})" >Comment</a>
            
POST;

            $post .= <<<POST
            <div id="post_id_{$postID}" class='hidden'>
                <div class='commentArea_{$postID}'>

POST;

            $commentResult = queryFunc("SELECT comment,CONCAT(first_name,last_name) as 'name',createdAt from comments inner join users on users.user_id = comments.user_id where comments.post_id ='$postID' order by createdAt");
            while ($comments = isRecord($commentResult)) {
                $diffTime = find_difference_of_time($comments['createdAt']);
                $timeToShow = create_time_string($diffTime);
                
                $post .= <<<POST
                <div class='comment'>
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
                    <input type="text" value={$postID} style="display:none" name="post_id_{$postID}">
                    <input type="text" value={$_SESSION['user']} style="display:none" name="post_user">
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
    if($timeDate < 60){
        if($timeDate == 1)
            return $timeDate ." Second Ago";
        else
            return $timeDate ." Seconds Ago";
        
    }
    else if($timeDate > 59 && $timeDate < 3599){
        if(($timeDate / 60) < 2)
            return round($timeDate / 60) . " Minute Ago";
        else
            return round($timeDate / 60) . " Minutes Ago"; 
    }
    else if($timeDate > 3599){
        if(($timeDate / 3660) < 2)
            return round($timeDate / 3600) . " Hour Ago";    
        else
            return round($timeDate / 3600) . " Hours Ago";         
        }
    else if($timeDate > 86399){
        if(($timeDate / 86400) < 2)
            return round($timeDate / 3600) . " Day Ago";    
        else
            return round($timeDate / 3600) . " Days Ago";         
    }
}




?>