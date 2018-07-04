<?php 
  
  // $name = $_FILES['file']['name'];  // Getting the name of the file
  // $tmp_name = $_FILES['file']['tmp_name'];    // Storing it at  temp location
  // $type = $_FILES['file']['type']; // Getting file type

  // echo $_POST['post'];

  // $typeS = explode('/',$type);
  // $result = uniqid();
   
  
  // // mkdir('assets/post126/',0777,true);
  //   $location = 'assets/';
  //    if (move_uploaded_file($tmp_name, $location.$result.'.'.$typeS[1])) {
         
  //        $path = $location.$result.'.'.$typeS[1];

  //        echo "<img src='$path' />";
  //    }
  // function addPost(user_id) {
  //   // Again the name suggests xD
  
  //   // Getting post content
  //   var post = document.querySelector("textarea[name='post']");
  //   var postPicData = document.querySelector("input[name='post-pic']");
  //   var postPic = postPicData.files[0];
  
  //   var postContent = post.value;
  
  //   console.log(postPic);
    
  //   if(!(postContent.trim() == '') || (postPic !== undefined)){
  //   var formData = new FormData();
  //   formData.append("file", postPic);
  //   formData.append("post", post.value);
  
  //   // Setting paramters for POST request
  //   // var param = `post=${post.value}&user_id=${user_id}`;
  
  //   ajaxCalls("POST", "post.php", formData, "pic").then(function(result) {
  //     // Adding new post to post Area
  //     // Adding post to the top not bottom. Clue xD
  //     document.querySelector(".posts").innerHTML =
  //       result + document.querySelector(".posts").innerHTML;
  //     document.querySelector("textarea[name='post']").value = " ";
  
  //     //Adding in recent activities
  //     var activity_type = 2;
  //     param = `activity_type=${activity_type}`;
  //     ajaxCalls("POST", `recentActivityAjax.php`, param).then(function(result) {
  //       addRecentActivity(result);
  //     });
  //   });
  // }
  //   // postPicData.value = '';
  //   document.querySelector(".pic-name").innerHTML = "";
  // }
  require_once('functions.php'); 
    echo hashString("");
?>