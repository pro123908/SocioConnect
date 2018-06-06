
<?php require_once('header.php');
 
if(isset($_GET['visitingUserID']) && isset($_SESSION['user_id'])){
// If both conditions are satisfied then you have come to this page by searching
    $flag = false;
    if(isset($_POST['add_friend']))
        addFriend($_GET['visitingUserID']);
    else if(isset($_POST['cancel_req']))
        cancelReq($_GET['visitingUserID']);
    else if(isset($_POST['respond_to_request'])) 
        redirection("requests.php");
    else if(isset($_POST['remove_friend']))
        removeFriend($_GET['visitingUserID']);    
}
else if(isset($_SESSION['user_id'])){
    // If this condition is true then you have come to the page by clicking on profile button on your profile - So you ain't searching anybody xD  
    $flag = true;
    $_SESSION['no_of_posts_changed'] = 0;
}
else{
    // Not authorized dude,go back to login page xD
    redirection("index.php"); // previously it was set to main.php
}
?>
<div class='user-timeline'>
    <div class='user-cover-area'>
        <?php $flag ? profilePic($_SESSION['user_id']) : profilePic($_GET['visitingUserID']) ?>
    </div>
    <div class='content-area'>
        <div class='user-info-area'>
        </div>
        <div class='post-area'>
            <div class='new-post'>
            <?php 
            // Add post functionality
            if(isset($_GET['visitingUserID']))
                addPost(false,$_GET['visitingUserID']); 
            else
                addPost(true,"abc");

            ?>
            </div>

            <div class='posts'>
  
            <?php
            $user = $flag ? 'b' : $_GET['visitingUserID'];
            showPosts($user,1,10);
            ?>

            </div>
    
            <?php
            if($flag){
                $show = true;
            }
            else{
                $show = isFriend($_GET['visitingUserID']) ? true : false;    
            }
            if($show){
                $showMoreButton = <<<MSG
                <div id='loading'><a href="javascript:showNextPage('{$user}')">Show More Posts</a></div>
MSG;
            echo $showMoreButton;
            }
            ?>
        </div>    
    <div class='friends-area'></div>
</div>

<script src="script.js" >

</script>