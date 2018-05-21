<?php include "header.php"; ?>


<?php if(isset($_GET['visiting']) && isset($_SESSION['user_id'])){
        $flag = false;
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

<div class='add_post_timeline'>
<?php  $flag ? addPost(true) : addPost(false) ?>
</div>

<div id='postArea'>
<h3>Your Posts</h3>
<?php $flag ? showPosts('b') : showPosts($_GET['visiting']) ?>
</div>

<script src="script.js" >

</script>