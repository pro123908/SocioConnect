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

function show_posts(){
    //Selecting all the posts in a manner where user_id matches post_id
    $queryResult = queryFunc("SELECT post,post_id,posts.user_id,CONCAT(first_name,last_name) as 'name' from posts inner join users on users.user_id = posts.user_id order by post_id desc");
   
    if (isData($queryResult)) {
        while ($row = isRecord($queryResult)) {
            $postID = $row['post_id'];
            
            //Getting likes count for the current post
            $likesResult = queryFunc("SELECT count(*) as count from likes where post_id='$postID'");
            $likes = isRecord($likesResult);
            
            $post = <<<POST
            <div class='post'>
                <span class='user'>{$row['name']}</span>
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
                $post .= <<<POST
                <div class='comment'>
                    <span class='commentUser'>{$comments['name']} : </span>
                    <span class='commentText'>{$comments['comment']}</span>
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





?>