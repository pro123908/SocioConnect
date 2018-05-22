<?php include "header.php"; ?>


<?php if(isset($_GET['visiting']) && isset($_SESSION['user_id'])){
        $flag = false;
        if(isset($_POST['add_friend']))
            addFriend($_GET['visiting']);
        else if(isset($_POST['cancel_req']))
            cancelReq($_GET['visiting']);
        else if(isset($_POST['respond_to_request'])) 
            redirection("requests.php");
        else if(isset($_POST['remove_friend']))
            removeFriend($_GET['visiting']);    
    }
    else if(isset($_SESSION['user_id'])){
        $flag = true;
    }
    else{
        redirection("main.php");
    }
?>

<div class="personal_info">
    <?php $flag ? personalInfo(true,"") : personalInfo(false,$_GET['visiting']) ?>
</div>

<div class="friend_button">
    <?php $flag ? showFriendButton(0) : showFriendButton($_GET['visiting']) ?>
</div>

<div class='add_post_timeline'>
<?php  $flag ? addPost(true,"") : addPost(false,$_GET['visiting']) ?>
</div>

<div id='postArea'>
<h3>Your Posts</h3>
<?php $flag ? showPosts('b') : showPosts($_GET['visiting']) ?>
</div>

<script src="script.js" >

</script>