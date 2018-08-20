<?php

require_once dirname(__FILE__) . '/includes/header.php';
if (!isset($_SESSION['user_id'])){
    redirection('index.php');
}

if(isset($_GET['id'])){
    $id = $_GET['id'];
?>

<div class='user-res-info row no-gutters'>

 <div class='user-info-area col-12 col-sm-12 col-md-12'>
                        <div class='user-info-heading'>User Details</div>
                        <div class='user-info-content'>
                            <?php showUserInfo($id);?>
                        </div>
</div>

<div class='user-activities-summary-area col-12 col-sm-12 col-md-12'>
                        <div class='user-activities-summary-heading'>Activites Summary</div>
                        <div class='user-activities-summary-content'>
                            <?php showUserActivitiesSummary($id);?>
                        </div>
                    </div>

 <?php if ($_SESSION['user_id'] == 1) {?>
                         <div class='remove-user-area col-12 col-sm-12 col-md-12'>
                            <div class='remove-user-heading'>Remove User</div>
                            <div class='remove-user-content'>
                                <input type="number" class= "remove-user-input" autocomplete = "off" placeholder="Enter User Id to remove account"><br><br>
                                <input type="button" class ="remove-user-submit" value= "Remove Account" onclick="deleteUser();">
                            </div>
                        </div>

                        <div class='latest-users-area  col-12 col-sm-12 col-md-12'>
                            <div class='latest-users-heading'>Latest Registered Users</div>
                            <div class='latest-users-content'>
                                <?php showLatestRegisteredUsers()?>
                            </div>
                        </div>    

                          <div class='user-activities-summary-area-for-admin col-12 col-sm-12 col-md-12'>
                            <div class='user-activities-summary-heading-for-admin'>Detailed Activites Summary Of Users</div>
                            <div class='user-activities-summary-input-area-for-admin'>
                                <input type="number" class= "search-user-details-input" autocomplete = "off" placeholder="Enter User Id..."><br><br>
                                <input type="button" class ="search-user-details-submit" value= "Get Details" onclick="getUserDetails();">
                            </div>
                            <div class='user-activities-summary-content-for-admin'>

                            </div>
                        </div>     
                    <?php }?>

                      

 <div class='recenet-uploads-area col-12 col-sm-12 col-md-12'>
                        <div class='recenet-uploads-heading'>Recent Uploads</div>
                        <div class='recenet-uploads-content'>
                            <?php getUploadedPics($id);?>
                        </div>
                        <div class='recent-uploads-footer'><?php if(isset($_SESSION['recent_uploads'])) echo "<p class='see-more'>No Recent Uploads</p>"; unset($_SESSION['recent_uploads'])?></div>
                    </div>

<?php if($id == $_SESSION['user_id']){ ?>
<div class='people-you-may-know-area col-12 col-sm-12 col-md-12'>
                        <div class='people-you-may-know-heading'> People you may know</div>
                        <div class='people-you-may-know-content'>
                            <?php showPeopleYouMayKnow()?>
                        </div>
                    </div>
<?php } ?>

</div>

<?php } else{
    redirection('main.php');
} ?>
<script src="./includes/script.js" ></script>