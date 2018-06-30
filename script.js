function setUserId(id) {
  var url = window.location.href;
  if (
    url == "http://localhost/socioConnect/main.php" ||
    url == "http://localhost/socioConnect/timeline.php" ||
    url.slice(0, 42) == "http://localhost/socioConnect/timeline.php"
  ) {
    var post = document.querySelectorAll(".post");
    //If there are no posts
    if (post.length == 0)
      document.getElementById("loading").innerHTML = "No Posts To Show";
    //If there are less than 10 posts
    else if (post.length < 10)
      document.getElementById("loading").innerHTML = "No More Posts To Show";
    //If there are 10 posts
    else {
      var flag = document.getElementById("noMorePosts");
      //if no more flag is true
      if (flag.value == "true")
        document.getElementById("loading").innerHTML = "No More Posts To Show";
      //if there are more posts present
      else {
        if (url == "http://localhost/socioConnect/timeline.php") {
          var loading = `<a href="javascript:showNextPage('b')">Show More Posts</a>`;
        } else if (
          url.slice(0, 42) == "http://localhost/socioConnect/timeline.php"
        ) {
          var id = url.slice(58);
          var loading = `<a href="javascript:showNextPage('${id}')">Show More Posts</a>`;
        } else {
          var loading = `<a href="javascript:showNextPage('a')">Show More Posts</a>`;
          // if(){
          //   var seeMoreActivites = "<a href='allActivities.php' class='see-more'><span>See more</span></a>";
          // }else{
          //   var seeMoreActivites = "<p class='see-more'>No Recent Activities</p>";
          // }
        }
        document.getElementById("loading").innerHTML = loading;
      }
    }
  } else if (url.slice(0, 42) == "http://localhost/socioConnect/messages.php") {
    var msgs = document.querySelectorAll(".chat-message");
    if (msgs.length == 0)
      document.getElementById("loading-messages").innerHTML =
        "No Messages To Show";
    else if (msgs.length < 10)
      document.getElementById("loading-messages").innerHTML =
        "No More Messages To Show";
    else {
      var flag = document.getElementById("noMoreMessages");
      if (flag.value == "true")
        document.getElementById("loading-messages").innerHTML =
          "No More Messages To Show";
      else {
        var id = url.slice(46);
        if (id > 0) {
          document.getElementById(
            "loading-messages"
          ).innerHTML = `<a href="javascript:showNextPageMessages('${id}')">Show More Messages</a>`;
        }
      }
    }
  } else if (url == "http://localhost/socioConnect/allNotification.php") {
    var notis = document.querySelectorAll(".notification");
    if (notis.length == 0)
      document.getElementById("loading-notis").innerHTML =
        "No Notifications To Show";
    else if (notis.length < 10)
      document.getElementById("loading-notis").innerHTML =
        "No More Notifications To Show";
    else {
      var flag = document.getElementById("noMoreNotis");
      if (flag.value == "true")
        document.getElementById("loading-notis").innerHTML =
          "No More Messages To Show";
      else {
        document.getElementById(
          "loading-notis"
        ).innerHTML = `<a href="javascript:showNextPageNotis()">Show More Notifications</a>`;
      }
    }
  } else if (url == "http://localhost/socioConnect/allActivities.php") {
    var notis = document.querySelectorAll(".recent_activity ");
    if (notis.length == 0)
      document.getElementById("loading-activities").innerHTML =
        "No Activities To Show";
    else if (notis.length < 10)
      document.getElementById("loading-activities").innerHTML =
        "No More Activities To Show";
    else {
      var flag = document.getElementById("noMoreActivities");
      if (flag.value == "true")
        document.getElementById("loading-activities").innerHTML =
          "No More Activities To Show";
      else {
        document.getElementById(
          "loading-activities"
        ).innerHTML = `<a href="javascript:showNextPageActivities()">Show More Activities</a>`;
      }
    }
  }
  session_user_id = id;
}

function showCommentField(id) {
  // Displaying comment section when comment button is clicked
  document.getElementById("comment-section-" + id).classList.toggle("hidden");
}

function like(postID) {
  
  ajaxCalls("GET", `like.php?like=${postID}`).then(function(result) {
    let value = result.trim();
    document.querySelector(
      `.like-count-${postID}`
    ).innerHTML = `<i class='like-count-icon fas fa-thumbs-up'></i> ${value}
    <span class='tooltip tooltip-bottom count'></span>`;

    let icon = document.querySelector(`.post-${postID} .like-btn i`);
    icon.classList.toggle("blue");
    //Adding in recent activities if liked

    if (icon.classList[2]) { // If it has a class blue xD
      var activity_type = 0; // Activity - like

      param = `target_id=${postID}&activity_type=${activity_type}`;
      ajaxCalls("POST", `recentActivityAjax.php`, param).then(function(result) {
        // Adding to the view
        addRecentActivity(result);
      });
    } else {

      // Means post has been disliked
      var activity_type = 4; // Unlike
      param = `target_id=${postID}&activity_type=${activity_type}`;
      
      ajaxCalls("POST", `recentActivityAjax.php`, param).then(function(result) {
        document.querySelector(".activities-content").innerHTML = result;
      });
    }
  });
}

function addRecentActivity(activity) {
  //Adding recent activity to the activity area
  // Getting the area
  var activitiesDiv = document.querySelector(".activities-content");

  // Inserting the new activity at the top
  activitiesDiv.innerHTML = activity + activitiesDiv.innerHTML;
  
  // If activities have become more than 10,then just delete the bottom one
  if (findChildNodes(activitiesDiv) == 11) {

    document.querySelector(".show-more-activities").innerHTML =
      "<a href='allActivities.php' class='see-more'><span>See more</span></a>";

    //Removing bottom activity from the list
    var lastChild = activitiesDiv.getElementsByTagName("a")[10];
    var removed = activitiesDiv.removeChild(lastChild);

  } else if (findChildNodes(activitiesDiv) > 0){
    // If activities were not more than 10
    document.querySelector(".show-more-activities").innerHTML =
      "<p class='see-more'>No More Activities to Show</p>";
  }
}

function findChildNodes(div) {
  // Count Child nodes the passed element
  var count = 0;
  for (i = 0; i < div.childNodes.length; i++) {
    if (div.childNodes[i].nodeType == 1) count++;
  }

  // Returning number of child nodes
  return count;
}

// function ajaxFetchCalls(url, data = {}) {
//   return new Promise(function(resolve, reject) {
//     fetch(url, data).then(function(data) {
//       resolve(data);
//     });
//   });
// }

// Function for making all ajax calls using promise
function ajaxCalls(method, pathString, postParam = "", pic = "") {
  // Creating promise
  return new Promise(function(resolve, reject) {
    // Creating XHR object for AJAX Call
    var xmlhttp = new XMLHttpRequest();

    // If response has arrived for request made
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == XMLHttpRequest.DONE) {
        // XMLHttpRequest.DONE == 4
        if (xmlhttp.status == 200) {
          // Return the response
          resolve(this.responseText);
        } else if (xmlhttp.status == 400) {
          // alert('REJECTEd : 400');
          reject("Rejected");
        } else {
          // alert('REJECTEd : other');
        }
      }
    };

    // Preparing request with method and filename
    xmlhttp.open(method, pathString, true);

    if (postParam && pic == "") {
      // Setting up headers to be send in POST request
      xmlhttp.setRequestHeader(
        "Content-type",
        "application/x-www-form-urlencoded"
      );
    }

    // Sending request
    xmlhttp.send(postParam);
  });
}

function comment(postID, user, profilePic) {
  // Adding comment to post

  // Getting targeted comemnt field
  var comment = document.querySelector(`input[name='comment_${postID}']`);
  

  // Extracting Value
  var commentValue = comment.value;

  var timeToShow = "Just Now";
  

  // Validating input to not to be empty
  if (!(commentValue.trim() == "")) {
    // Setting up parameters for POST request to the file
    var param = `comment=${comment.value}&post_id=${postID}`;

    ajaxCalls("POST", "comment.php", param).then(function(result) {
      // Added Comment ID is returned in response
      commentID = result.trim();

      
      document.querySelector(`.comment-area-${postID}`).innerHTML += `
  <div class='comment comment-${commentID}'>
                
  <div class='user-image'>
      <img src='${profilePic}' class='post-avatar post-avatar-30' />
  </div>
  
  <div class='comment-info'>
  <i class='tooltip-container far fa-trash-alt comment-delete' onclick='javascript:deleteComment(${commentID})'><span class='tooltip tooltip-right'>Remove</span></i>
  <i class="tooltip-container fas fa-edit comment-edit" onclick="javascript:editComment(${commentID},${postID},'${profilePic}','${timeToShow}')"><span class='tooltip tooltip-right'>Edit</span></i>
  <div class='comment-body'>
  <span class='comment-user'>${user} : </span>
  <span class='comment-text'>${comment.value}</span>
  <span class='comment-time'>${timeToShow}</span>
  </div>
  
  </div>
</div>
  `;

      comment.value = "";
    

      //Adding in recent activities
      var activity_type = 1; // Comment Activity
      // targeted Content
      var commentDetails = postID + " " + commentID;
      param = `target_id=${commentDetails}&activity_type=${activity_type}`;
      ajaxCalls("POST", `recentActivityAjax.php`, param).then(function(result) {
        addRecentActivity(result);
      });
    });
  }
  // Pain in the ass xD
  return false;
}

function deletePost(postID) {
  // As the name suggests

  ajaxCalls("GET", `delete.php?id=${postID}`).then(function(result) {
    document.querySelector(`.post-${postID}`).style.display = "none";
  });
}

function postPicSelectedName() {
  // Getting the name of pic selected
  var postPic = document.querySelector("input[name='post-pic']").files[0];
  // Displaying name of selected pic
  document.querySelector(".pic-name").innerHTML = postPic.name;
  
}

function addPost(userID) {
  // Again the name suggests xD

  // Getting post input field
  var post = document.querySelector("textarea[name='post']");
  // Getting pic input field
  var postPicData = document.querySelector("input[name='post-pic']");
  // Getting pic uploaded
  var postPic = postPicData.files[0];

  // Post Value
  var postContent = post.value;

  // Making sure that post is not empty by any means
  if (!(postContent.trim() == "") || postPic !== undefined) {

    // Preparing form data to send
    var formData = new FormData();
    //Pic Added
    formData.append("file", postPic);
    // Post Value added
    formData.append("post", post.value);

    // Sending POST request to post.php
    ajaxCalls("POST", "post.php", formData, "pic").then(function(result) {
      // Adding new post to post Area
      // Adding post to the top not bottom. Clue xD
      document.querySelector(".posts").innerHTML =
        result + document.querySelector(".posts").innerHTML;
      
        // Clearing the post input
      document.querySelector("textarea[name='post']").value = " ";

      //Adding in recent activities
      var activity_type = 2; // Post activity
      param = `activity_type=${activity_type}`;
      ajaxCalls("POST", `recentActivityAjax.php`, param).then(function(result) {
        addRecentActivity(result);
      });
    });
  }
  
  // Removing the name of pic selected
  document.querySelector(".pic-name").innerHTML = "";
}

function hideEditDiv(postID, flag) {
  var parentDiv = document.querySelector(".post-content-" + postID);
  document.querySelector(".actual-post-" + postID).style.display = "block";
  parentDiv.removeChild(document.querySelector(".edit-post-" + postID));
  if (flag)
    document.querySelector(".post-edited-" + postID).innerHTML = "Edited";
}

//Copied this function from above postPicSelected, just to check right now, in future both will be merged
function editedPostPicSelected(postID) {
  var editForm = document.querySelector(".edit-post-" + postID);
  var postPic = editForm.querySelector("input[name='post-pic']").files[0];
  editForm.querySelector(".pic-name").innerHTML = postPic.name;
  console.log(postPic.name);
}

function showFileUpload(postID) {
  var editForm = document.querySelector(".edit-post-" + postID);
  editForm.querySelector(".upload-btn-wrapper").style.display = "inline-block";
}

function hideFileUpload(postID) {
  var editForm = document.querySelector(".edit-post-" + postID);
  editForm.querySelector(".upload-btn-wrapper").style.display = "none";
}

function editPost(postID) {
  if (!document.querySelector(".edit-post-" + postID)) {
    //Current Post
    var post = document.querySelector(".actual-post-" + postID);

    //Current Post and picture
    var postPic = post.querySelector(".post-image-container");
    var postContent = post.getElementsByTagName("p")[0];

    //Hiding current post
    post.style.display = "none";

    //Creating a new div to display if it doesn't exist and get the edit input and inserting it in the same parent div, just before the div where text and pic were shown
    if (!document.querySelector(".edit-post-" + postID)) {
      var div = document.createElement("div");
      div.setAttribute("class", "edit-post-" + postID);
      div.innerHTML = `<form action="editPost.php" method='POST'>
          <textarea name="post" id="" cols="30" rows="10" class="post-input post-edit-${postID}">${
        postContent.innerHTML
      }</textarea>
          <br>
          <div class ="radio-buttons-edit">
            <label><input type="radio" name="edit-post-pic" value="remove" onclick="hideFileUpload(${postID})"> Remove Current Photo</label><br>
            <label><input type="radio" name="edit-post-pic" value="keep" onclick="hideFileUpload(${postID})"> Keep Current Pic</label><br>
            <label><input type="radio" name="edit-post-pic" value="new" onclick="showFileUpload(${postID})"> Upload New Photo</label><br>
          </div> 
          <div class='upload-btn-wrapper' style="display:none;">
            <button class='pic-upload-btn'><i class='far fa-image'></i></button>
            <input type='file' name='post-pic' onchange='javascript:editedPostPicSelected(${postID})'  />
            <span class='pic-name'></span>
          </div>               
          <div class='post-btn-container'>
            <a  href="javascript:hideEditDiv(${postID},false)"  class='edit-post-cancel-btn'>Cancel</a>
            <a  href="javascript:saveEditPost(${postID})"  class='edit-post-save-btn'>Save</a>
          </div>
        </form>`;

      var parentDivForEditingArea = document.querySelector(
        ".post-content-" + postID
      );
      parentDivForEditingArea.insertBefore(div, post);
    }
  }
}
function saveEditPost(postID) {
  //Getting post edit text
  var postContent = document.querySelector(".post-edit-" + postID);

  //Getting div in which edited values are present
  var editForm = document.querySelector(".edit-post-" + postID);

  //Getting picutre file
  var postPicData = editForm.querySelector("input[name='post-pic']");
  var postPic = postPicData.files[0];

  //If niether text nor pic are inserted
  if (!(postContent.value.trim() == "") || postPic !== undefined) {
    if (editForm.querySelector('input[name="edit-post-pic"]:checked')) {
      //Getting radio button value
      var action = editForm.querySelector('input[name="edit-post-pic"]:checked')
        .value;

      //If new is selected by no path is given for image
      if (
        action == "new" &&
        editForm.querySelector(".pic-name").innerHTML == ""
      ) {
        alert("Select Image to change image");
        return 0;
      }

      //Preparing formData for ajax call
      var formData = new FormData();
      formData.append("file", postPic);
      formData.append("postID", postID);
      formData.append("postContent", postContent.value);
      formData.append("action", action);

      ajaxCalls("POST", "postEdit.php", formData, "pic").then(function(result) {
        //Displaying original post div which was made hidden in previous function
        var post = document.querySelector(".actual-post-" + postID);
        //Storing edited status in the p tag
        post.getElementsByTagName("p")[0].innerHTML = postContent.value;
        
        //result CONTAINS THE PATH OF IMAGE
        //checking if the response path is empty, i.e no image is to be shown
        var imgDiv = post.querySelector(".post-image");
        if (result.trim() != "") {
          // div for image is already present then only updating its src, making display block bcozit might be made none due to line 443 (See if condition in the else block)
          if (imgDiv) {
            imgDiv.style.display = "block";
            imgDiv.src = result;
          }

          //if div isn't present then creating it from scratch
          else {
            var imgParentDiv = document.querySelector(".actual-post-" + postID);
            imgParentDiv.innerHTML += `<div class='post-image-container'><img src='${result}' class='post-image' /></div>`;
          }
        } else {
          //response was empty this means that there is no picture to show, so first check, if div for images is present then hide it
          if (imgDiv) imgDiv.style.display = "none";
        }

        //Now hide the editing div and write Edited in the header section of the post
        hideEditDiv(postID, true);
      });
    } else alert("Select Action to do on the image");
  } else {
    alert("Enter either a text or an image");
  }
}

function deleteComment(commentID) {
  // Deleting Comment specified by comment ID

  ajaxCalls("GET", `commentDelete.php?id=${commentID}`).then(function(result) {
    document.querySelector(`.comment-${commentID}`).style.display = "none";
  });
}

function saveEditComment(postID, commentID, user, profilePic, time) {
  // Adding comment to post
  // Getting post ID,content and user who posted comment from form

  // Getting edited comment
  var comment = document.querySelector(
    `input[name='comment_edit_${commentID}']`
  );
  // Setting up parameters for POST request to the file
  var param = `comment=${comment.value}&comment_id=${commentID}`;
  

  ajaxCalls("POST", "commentEdit.php", param)
    .then(function(result) {
      
      showComment(user, commentID, postID, profilePic, time, comment.value,true);
    })
    .catch(function(reject) {
      console.log("REJECTED");
    });

  
  return false;
}

function editComment(commentID, postID, profilePic, time) {
  // Getting comment 
  var comment = document.querySelector(".comment-" + commentID);
  // Getting user of that comment
  var user = comment.querySelector(".comment-user").innerHTML;
  // Slicing name "Bilal Ahmad" from "Bilal Ahmad : "
  user = user.slice(0, user.length - 3);
  // Getting old value of comment
  var commentValue = comment.querySelector(".comment-text").innerHTML;
  var currentComment = comment.innerHTML;
  
  comment.innerHTML = `
    <div class='comment-form'>
      <form onsubmit ="return saveEditComment(${postID},${commentID},'${user}','${profilePic}','${time}')"  method="post" id='commentFormEdit_${commentID}'>
          <input name = "comment_edit_${commentID}" type='text' autocomplete = "off" value = "${commentValue}" style="width:500px">
          <i class='tooltip-container fas fa-times comment-delete' onclick="javascript:showComment('${user}',${commentID},'${postID}','${profilePic}','${time}','${commentValue}',false)"><span class='tooltip tooltip-right'>Cancel</span></i>
          <input style='display:none;' type="submit" id="${postID}" value="Comment" > 
      </form>
    </div>`;
}

function showComment(user, commentID, postID, profilePic, time, comment,flag) {
  
  if(flag) // Comment is edited
    var edited = "Edited";
  else
    var edited = "";   

  // Inserting the comment
  document.querySelector(`.comment-${commentID}`).innerHTML = `
    <div class='user-image'>
      <img src='${profilePic}' class='post-avatar post-avatar-30' />
    </div>
    <div class='comment-info'>
      <i class='tooltip-container far fa-trash-alt comment-delete' onclick='javascript:deleteComment(${commentID})'><span class='tooltip tooltip-right'>Remove</span></i>
      <i class="tooltip-container fas fa-edit comment-edit" onclick="javascript:editComment(${commentID},${postID},'${profilePic}','${time}')"><span class='tooltip tooltip-right'>Edit</span></i>
      <div class='comment-body'>
        <span class='comment-user'>${user} : </span>
        <span class='comment-text'>${comment}</span>
        <span class='comment-time'>${time}</span>
        <span class='comment-edit-text'>${edited}</span>   
      </div>
    </div>
  
  `;
}
//DP Animation Functions
function onClosedImagModal() {
  // When model is closed
  var modal = document.getElementById("modal");
  modal.classList.remove("modal-open");
  modal.classList.add("modal-close");

  // Timeout in displaying
  setTimeout(() => {
    modal.style.display = "none";
  }, 550);
}
function showImage() {
  // Showing pic in the model
  var modal = document.getElementById("modal");
  modal.classList.add("modal-open");
  modal.classList.remove("modal-close");
  modal.style.display = "block";
  document.getElementById("modal-img").src = document.getElementById(
    "profile_picture"
  ).src;
}

function changePic() {
  // Changing Pic xD
  var formPic = document.querySelector(".formPic");
  formPic.classList.toggle("show");
  formPic.classList.toggle("hidden");
}

//Search Function
function getUsers(value, flag) {
  // flag values :
  // 0 - Searching in messages
  // 1 - Normal Searching

  // Setting paramters for POST request
  var param = `query=${value}&flag=${flag}`;
  var searchFooter;

  ajaxCalls("POST", "search.php", param).then(function(result) {
    if (result == "No") {
      // If no user found against search
      document.querySelector(".search-result").style.display = "none";
    } else {
      // Displaying search results for normal search
      if (flag == 1) {
        document.querySelector(".search-result").style.display = "block";
        document.querySelector(".search-result").innerHTML = result;

        if (value.length == 0) {
          document.querySelector(".search-result").style.display = "none";
          searchFooter = "";
        } else {
          searchFooter = `<a class='see-more' href='allSearchResults.php?query=${value}'>See more</a>`;
          document.querySelector(".search-result").innerHTML += searchFooter;
        }
      }
      // Displaying search results for searching in messages.php
      else if (flag == 0) {
        document.querySelector(".search-result-message").style.display =
          "block";
        document.querySelector(".search-result-message").innerHTML = result;

        if (value.length == 0) {
          document.querySelector(".search-result-message").style.display =
            "none";
        }
      }
    }
  });
  // Pain in the ass
  return false;
}

setInterval(commentsRefresh, 1000);

function commentsRefresh() {
  ajaxCalls("GET", "commentsAjax.php").then(function(result) {
    // Displaying search results
    var data = JSON.parse(result);
    for (i = 0; i < data.length; i++) {
      var obj = data[i];

      var comment = `
         <div class='comment comment-${obj.postID}'>
         <span class='comment-user'>${obj.name} : </span>
         <span class='comment-text'>${obj.comment}</span>
         <span class='comment-time'>Just now</span>
     </div>
         `;

      document.querySelector(
        `.comment-area-${obj.postID}`
      ).innerHTML += comment;
    }
  });
}

//setInterval(notificationRefresh, 3000);

function notificationRefresh() {
  ajaxCalls("GET", "notificationsAjax.php").then(function(result) {
    // Displaying search results
    var data = JSON.parse(result);
    for (i = 0; i < data.length; i++) {
      var obj = data[i];
      var notification = "";

      if (obj.type == "post") {
        notification = `
        <a href='notification.php?postID=${obj.postID}&type=${
          obj.type
        }&notiID=${obj.notiID}'>${obj.name} has posted<br><br></a>
         `;
      } else {
        notification = `
       <a href='notification.php?postID=${obj.postID}&type=${obj.type}&notiID=${
          obj.notiID
        }'>${obj.name} has ${obj.type} your post<br><br></a>
        `;
      }

      document.querySelector(`.notifications`).innerHTML =
        notification + document.querySelector(`.notifications`).innerHTML;
    }
  });
}

setInterval(likesRefresh, 3000);

function likesRefresh() {
  ajaxCalls("GET", "likesAjax.php").then(function(result) {
    // Displaying search results
    var data = JSON.parse(result);

    for (i = 0; i < data.length; i++) {
      var obj = data[i];

      document.querySelector(`.like-count-${obj.postID}`).innerHTML = obj.likes;
    }
  });
}

function likeUsers(postID) {
  console.log('Inside like USers');
  ajaxCalls("GET", `likeUsers.php?postID=${postID}`).then(function(result) {
    // Displaying search results
    document.querySelector(`.like-count-${postID} .count`).innerHTML = "";
    var data = JSON.parse(result);
    let flag = true;

    console.log('Inside AJAX success');

    for (i = 0; i < data.length; i++) {
      console.log('Inside loop');
      flag = false;
      var obj = data[i];
      
      document.querySelector(`.like-count-${postID} .count`).innerHTML += `${
        obj.name
      }<br>`;
    }
    if (flag) {
      console.log('No records found');
      // document
      //   .querySelector(`.like-count-${postID} .count`)
      //   .classList.remove("tooltip");

        // This will remove all the classes from the element
        // document.querySelector(`.like-count-${postID} .count`).className = 'count';
      
    }
  });
}

function hideLikers(postID) {
  // Hiding likers when clicked on number again
  document.querySelector(`.like-count-${postID} .count`).innerHTML = "";
}

function message() {
  let messageBody = document.messageForm.message_body;
  let partner = document.messageForm.partner;
  let pic = document.messageForm.pic;

  var width = document.querySelector(".chat-messages").offsetWidth;
  var widthX = document.querySelector(".chat-messages .message");

  console.log("width : " + width * 0.8);
  console.log(messageBody.value.length);
  if (messageBody.value.length > 0) {
    let param = `partner=${partner.value}&messageBody=${messageBody.value}`;

    document.querySelector(".chat-messages").innerHTML += `
      <div class="chat-message my-message">
        <img src='${pic.value}' class='post-avatar post-avatar-30' />
        <span class='message'>${messageBody.value}</span>
        <span class='message-time'>Just now</span>
      </div>
      `;

    ajaxCalls("POST", "messageAjax.php", param).then(function(response) {
      console.log("Response messageSimple : " + response);
      var msgs = document.querySelectorAll(".chat-message");
      if (
        document.getElementById("loading-messages").innerHTML ==
        "No Messages To Show"
      )
        document.getElementById("loading-messages").innerHTML =
          "No More Messages To Show";
    });

    messageBody.value = "";

    var last = document.querySelector(".my-message:last-child");
    // var last = nodes[nodes.length - 1];

    last.scrollIntoView();
  }
}

setInterval(messageRefresh, 1000);

function messageRefresh() {
  var url = window.location.href;
  var id = url.substring(url.lastIndexOf("=") + 1);

  ajaxCalls("GET", `messageAjax.php?id=${id}`).then(function(response) {
    let messageResponse = JSON.parse(response);

    for (i = 0; i < messageResponse.length; i++) {
      let obj = messageResponse[i];

      document.querySelector(".chat-messages").innerHTML += `
      <div class='chat-message their-message'>
            <img src='${obj.pic}' class='post-avatar post-avatar-30' />
            <span class='message'>${obj.message}</span>
            <span class='message-time'>Just now</span>
        </div>
       `;

      var last = document.querySelector(".their-message:last-child");
      // var last = nodes[nodes.length - 1];

      last.scrollIntoView();
    }
  });
}

function refreshRecentConvos() {
  ajaxCalls("GET", "recentConvoAjax.php").then(function(result) {
    var data = JSON.parse(result);

    if (!(data.notEmpty == "Bilal")) {
      // console.log(data);
      for (i = data.length - 1; i >= 0; i--) {
        var obj = data[i];
        if (
          document.querySelector(".recent-chats .recent-user-" + obj.fromID)
        ) {
          document.querySelector(
            ".recent-chats .recent-user-" + obj.fromID
          ).style.display =
            "none";
        }
        var recentMessage = `
        <a href='messages.php?id=${
          obj.fromID
        }' class='recent-user recent-user-${obj.fromID}'>
          <span class='recent-user-image'>
            <img src='${obj.pic}' class='post-avatar post-avatar-40' />
          </span>
          <span class='recent-message-info'>
            <span class="recent-username">${obj.partner}</span>
            <span class='recent-message-text'>${obj.from} ${obj.msg}</span>
            <span class='recent-message-time'>${obj.at}</span>
          </span>
          <i class='tooltip-container far fa-trash-alt  comment-delete' onclick='javascript:deleteConvo(${
            obj.fromID
          })'><span class='tooltip tooltip-left'>Delete</span></i>
        </a>
        `;
        if (document.querySelector(".recent-chats")) {
          document.querySelector(".recent-chats").innerHTML =
            recentMessage + document.querySelector(".recent-chats").innerHTML;
        }
      }
    }
  });
}
setInterval(refreshRecentConvos, 1000);

function deleteConvo(id) {
  // For deleting User Chat
  // id - loggedIn userID

  var url = window.location.href; // URL of the current window
  var openConvoId = url.slice(46); // Will give the ID of the user whom you are chatting with

  let param = `id=${id}&urlID=${openConvoId}`;

  ajaxCalls("POST", `deleteConvoAjax.php`, param).then(function(response) {
    //If response is not a redirection, this would be changed if this comment is removed from messags.php
    if (response == "Reload the page") {
      window.location = "messages.php"; // Redirect the user to message Page
    } else {
      document.querySelector(".recent-chats").innerHTML = response;
    }
  });
}

function showPageMessages(id, page) {
  document.getElementById("loading-messages").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "loadMessagesAjax.php?id=" + id + "&page=" + page, true);
  xhr.onload = function() {
    if ((this.status = 200)) {
      document.querySelector(".chat-messages").innerHTML =
        this.responseText + document.querySelector(".chat-messages").innerHTML;
      document.getElementById("loading-messages").style.display = "block";
    }
    if (document.getElementById("noMoreMessages").value == "true")
      document.getElementById("loading-messages").innerHTML =
        "No More Messages To Show";
  };
  xhr.send();
}

function showNextPageMessages(id) {
  var noMorePosts = document.getElementById("noMoreMessages");
  var page = document.getElementById("nextPageMessages");
  if (noMorePosts.value == "false") {
    //deleting previous data
    var div = document.querySelector(".chat-messages");
    div.removeChild(page);
    div.removeChild(noMorePosts);

    showPageMessages(id, page.value);
  } else {
    alert("khtm");
  }
}

function removeFriend(id) {
  var url = window.location.href;
  
  let param = `friendId=${id}`;
  ajaxCalls("POST", "removeFriendAjax.php", param).then(function(result) {
    var data = JSON.parse(result);
    // if(data.length == 0){
    //   document.querySelector(".friends-list-elements").innerHTML = "";
    // }
    // else{
    console.log("Response messageSimple : " + data[0]);
    document.querySelector(".friends-container").innerHTML = "";
    var flag = 0;
    console.log(data.length);
    for (i = 0; i < data.length; i++) {
      flag++;
      var obj = data[i];
      var friend = `
        <div class="friend-container">
          <div class='friend'>
            <div class='friend-image'>
              <img class='post-avatar post-avatar-30' src='${
                obj.profile_pic
              }'  >
            </div>
            <div class='friend-info'>
              <a href="timeline.php?visitingUserID=${
                obj.user_id
              }" class='friend-text'>${obj.name}</a>   
              <span class='state-off'>${obj.time}</span>         
            </div>
            <div class='friend-action'>
            <div>
              <a href="javascript:removeFriend(${
                obj.user_id
              })" class='remove-friend'><i class="fas fa-times tooltip-container"><span class='tooltip tooltip-right'>Remove Friend</span></i></a>
              </div>
            </div>
          </div> 
          </div>  
        `;
      document.querySelector(".friends-container").innerHTML += friend;
      if (flag == 10 && url != "http://localhost/socioConnect/requests.php")
        break;
    }
    if (flag == 0) {
      document.querySelector(".show-more-friends").innerHTML =
        "<p class='see-more'>No Friends To Show</p>";
    } else if (flag == 10) {
      document.querySelector(".show-more-friends").innerHTML =
        "<a href='requests.php' class='see-more'><span>See more</span></a>";
    } else {
      document.querySelector(".show-more-friends").innerHTML =
        "<p class='see-more'>No More Friends To Show</p>";
    }
    // }
  });
}

function showPage(flag, page) {
  document.getElementById("loading").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET", `loadPostsAjax.php?flag=${flag}&page=${page}`, true);
  xhr.onload = function() {
    if ((this.status = 200)) {
      document.querySelector(".posts").innerHTML += this.responseText;
      document.getElementById("loading").style.display = "block";
    }
    if (document.getElementById("noMorePosts").value == "true")
      document.getElementById("loading").innerHTML = "No More Posts To Show";
  };
  xhr.send();
}

function showFirstPage(flag) {
  showPage(flag, 1);
}

function showNextPageCaller(flag) {
  //if user has scrolled to the bottom of the page
  if (true)
    setTimeout(function() {
      showNextPage(flag);
    }, 2000);
}

function showNextPage(flag) {
  //Fetching page no and flag to find whether more post are availible or not
  var noMorePosts = document.getElementById("noMorePosts");
  var page = document.getElementById("nextPage");
  if (noMorePosts.value == "false") {
    //deleting previous data
    var div = document.querySelector(".posts");
    div.removeChild(page);
    div.removeChild(noMorePosts);

    showPage(flag, page.value);
  }
}

function hello() {
  alert("hello");
}

function showPageNotis(page) {
  document.getElementById("loading-notis").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "loadNotificationsAjax.php?page=" + page, true);
  xhr.onload = function() {
    if ((this.status = 200)) {
      document.querySelector(".notifications").innerHTML =
        document.querySelector(".notifications").innerHTML + this.responseText;
      document.getElementById("loading-notis").style.display = "block";
    }
    if (document.getElementById("noMoreNotis").value == "true")
      document.getElementById("loading-notis").innerHTML =
        "No More Notifications To Show";
  };
  xhr.send();
}

function showNextPageNotis() {
  var noMorePosts = document.getElementById("noMoreNotis");
  var page = document.getElementById("nextPageNotis");
  if (noMorePosts.value == "false") {
    //deleting previous data
    var div = document.querySelector(".notifications");
    div.removeChild(page);
    div.removeChild(noMorePosts);
    showPageNotis(page.value);
  } else {
    alert("khtm");
  }
}

function showPageActivities(page) {
  document.getElementById("loading-activities").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET", "loadRecentActivitiesAjax.php?page=" + page, true);
  xhr.onload = function() {
    if ((this.status = 200)) {
      document.querySelector(".activities").innerHTML =
        document.querySelector(".activities").innerHTML + this.responseText;
      document.getElementById("loading-activities").style.display = "block";
    }
    if (document.getElementById("noMoreActivities").value == "true")
      document.getElementById("loading-activities").innerHTML =
        "No More Activities To Show";
  };
  xhr.send();
}

function showNextPageActivities() {
  var noMorePosts = document.getElementById("noMoreActivities");
  var page = document.getElementById("nextPageActivities");
  if (noMorePosts.value == "false") {
    //deleting previous data
    var div = document.querySelector(".activities");
    div.removeChild(page);
    div.removeChild(noMorePosts);
    showPageActivities(page.value);
  } else {
    alert("khtm");
  }
}

// Function for controlling dropdowns
function toggleDropdown(type) {
  // type:
  // Notification,Message,Request

  // Getting the Dropdown
  let display = document.querySelector(type).style.display;

  if (display == "block") {
    document.querySelector(type).style.display = "none";
  } else {
    document.querySelector(type).style.display = "block";
  }
}

/*  --------------- Closing Dropdowns when other areas are clicked ------------------ */
window.onclick = function(e) {
  if (e.srcElement.className != "search-input") {
    document.querySelector(".search-result").style.display = "none";
  }

  // Will loop through the array of dropdowns
  let arr = ["noti", "msg", "req"];
  arr.forEach(function(value) {
    if (
      !e.srcElement.classList.contains(`${value}-click`) &&
      !e.srcElement.classList.contains(`${value}-dropdown`)
    ) {
      document.querySelector(`.${value}-dropdown`).style.display = "none";
    }
  });
};

/* ------------------------------------------------------------------------------ */
