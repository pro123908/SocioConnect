function setUserId(id) {
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
    ).innerHTML = `<i class='like-count-icon fas fa-thumbs-up'></i> ${value}`;
    let icon = document.querySelector(`.post-${postID} .like-btn i`);
    icon.classList.toggle("blue");
  });
}

// function ajaxFetchCalls(url, data = {}) {
//   return new Promise(function(resolve, reject) {
//     fetch(url, data).then(function(data) {
//       resolve(data);
//     });
//   });
// }

// Function for making all ajax calls using promise
function ajaxCalls(method, pathString, postParam = "") {
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
          reject("Rejected");
        } else {
        }
      }
    };

    // Preparing request with method and filename
    xmlhttp.open(method, pathString, true);

    if (postParam) {
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

function comment(postID) {
  // Adding comment to post

  // Getting post ID,content and user who posted comment from form
  var post = document.querySelector(`input[name='post_id_${postID}']`);
  var comment = document.querySelector(`input[name='comment_${post.value}']`);
  var user = document.querySelector('input[name="post_user"]');
  var profilePic = document.querySelector('input[name="pic_user"]');

  // Setting up parameters for POST request to the file
  var param = `comment=${comment.value}&post_id=${post.value}`;

  ajaxCalls("POST", "comment.php", param).then(function(result) {
    // Added Comment ID is returned in response
    commentID = result.trim();

  //   // Rendering the added comment in post
  //   document.querySelector(`.comment-area-${post.value}`).innerHTML += `
  //       <div class='comment comment-${commentID}'>
  //       <i class='far fa-trash-alt comment-delete' onclick="javascript:deleteComment(${commentID})"></i>
  //         <span class='comment-user'>${user.value} : </span>
  //         <span class='comment-text'>${comment.value}</span>
  //         <span class='comment-time'>Just now</span>
  //       </div>
  //  `;
    console.log(profilePic.value);
  document.querySelector(`.comment-area-${post.value}`).innerHTML += `
  <div class='comment comment-${commentID}'>
                
  <div class='user-image'>
      <img src='${profilePic.value}' class='post-avatar post-avatar-30' />
  </div>
  
  <div class='comment-info'>
  <i class='far fa-trash-alt comment-delete' onclick="javascript:deleteComment(${commentID})"></i>
  <div class='comment-body'>
  <span class='comment-user'>${user.value} : </span>
  <span class='comment-text'>${comment.value}</span>
  <span class='comment-time'>Just now</span>
  </div>
  
  </div>
</div>
  `
   
    comment.value = "";
  });

  // Pain in the ass xD
  return false;
}

function deletePost(postID) {
  // As the name suggests

  ajaxCalls("GET", `delete.php?id=${postID}`).then(function(result) {
    document.querySelector(`.post-${postID}`).style.display = "none";
  });
}

function addPost(user_id) {
  // Again the name suggests xD

  // Getting post content
  var post = document.querySelector("textarea[name='post']");

  // Setting paramters for POST request
  var param = `post=${post.value}&user_id=${user_id}`;

  ajaxCalls("POST", "post.php", param).then(function(result) {
    // Adding new post to post Area
    // Adding post to the top not bottom. Clue xD
    document.querySelector(".posts").innerHTML =
      result + document.querySelector(".posts").innerHTML;
  });
}

function deleteComment(commentID) {
  // Deleting Comment specified by comment ID

  ajaxCalls("GET", `commentDelete.php?id=${commentID}`).then(function(result) {
    document.querySelector(`.comment-${commentID}`).style.display = "none";
  });
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
}

function changePic() {
  // Changing Pic xD
  var formPic = document.querySelector(".formPic");
  formPic.classList.toggle("show");
  formPic.classList.toggle("hidden");
}

//Search Function

function getUsers(value, flag) {
  // Setting paramters for POST request
  var param = `query=${value}&flag=${flag}`;
  var searchFooter;
  ajaxCalls("POST", "search.php", param).then(function(result) {
    // Displaying search results for normal search
    if (flag == 1) {
      document.querySelector(".search_results").innerHTML = result;
      if (value.length == 0) searchFooter = "";
      else
        searchFooter = `<form method="GET" action="allSearchResults.php"><input type="hidden" name="query" value=${value}><input type="submit" value="View All Results For ${value}"></form>`;
      document.querySelector(
        ".search_results_footer_empty"
      ).innerHTML = searchFooter;
    }
    // Displaying search results for searching in messages.php
    else if (flag == 0)
      document.querySelector(".search_results_for_messages").innerHTML = result;
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

setInterval(notificationRefresh, 3000);

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
  ajaxCalls("GET", `likeUsers.php?postID=${postID}`).then(function(result) {
    // Displaying search results
    document.querySelector(`.like-users-${postID}`).innerHTML = "";
    var data = JSON.parse(result);

    for (i = 0; i < data.length; i++) {
      var obj = data[i];

      document.querySelector(`.like-users-${postID}`).innerHTML +=
        " " + obj.name + " " + "|";
    }
  });
}

function hideLikers(postID) {
  // Hiding likers when clicked on number again
  document.querySelector(`.like-users-${postID}`).innerHTML = "";
}

function message() {
  let messageBody = document.messageForm.message_body;
  let partner = document.messageForm.partner;

  //to add a new user in recent contact list
  // var flag = 0;
  // var partnerName = document.getElementById("partner_heading").innerHTML;
  // partnerName = partnerName.slice(partnerName.indexOf(" and ")+5);

  // var usernames = document.querySelectorAll(".recent_username");
  // alert(partnerName);
  // alert(usernames.length)
  //  for(i=0; i<usernames.length; i++){
  //   if(usernames[i].value == partnerName)
  //       flag = 1;
  //       alert(usernames[i].value);
  // }
  // if(flag == 0)
  //   alert("New user found");
  // else
  //   alert("No new found");
  let param = `partner=${partner.value}&messageBody=${messageBody.value}`;

  document.querySelector("#convo_area").innerHTML += `
      <div id='green'>${messageBody.value}</div><hr>
     `;

  ajaxCalls("POST", "messageAjax.php", param).then(function(response) {
    console.log("Response messageSimple : " + response);
    // let messageResponse = JSON.parse(response);

    // document.querySelector("#messages_area").innerHTML += `
    //   <div id='green'>${messageResponse.message}</div><hr>
    //  `;
  });

  messageBody.value = "";
}

setInterval(messageRefresh, 1000);

function messageRefresh() {
  var url = window.location.href;
  var id = url.substring(url.lastIndexOf("=") + 1);

  ajaxCalls("GET", `messageAjax.php?id=${id}`).then(function(response) {
    let messageResponse = JSON.parse(response);

    for (i = 0; i < messageResponse.length; i++) {
      let obj = messageResponse[i];

      document.querySelector("#convo_area").innerHTML += `
        <div id='blue'>PartnerID : ${obj.partnerID}   ${obj.message}</div><hr>
       `;
    }
  });
}

function refreshRecentConvos(){
  
  ajaxCalls("GET", "recentConvoAjax.php").then(function(result) {
    var data = JSON.parse(result);
    if(!(data.notEmpty == "Bilal")){ 
      for (i = data.length-1; i >= 0; i--) {
        var obj = data[i];
        if(document.querySelector(".recent_user_"+obj.fromID))
           document.querySelector(".recent_user_"+obj.fromID).style.display = "none";
        var recentMessage = `
          <div class='recent_user recent_user_${obj.fromID}'>
            <a href='messages.php?id=${obj.fromID}'><button class="recent_username" >${obj.partner}</button></a>
            <p>${obj.from}:${obj.msg}</p>
            <p>${obj.at}</p>
          </div>
           `;
      document.querySelector(".recent_chats").innerHTML = recentMessage + document.querySelector(".recent_chats").innerHTML;
      }  
    }
  });
}
setInterval(refreshRecentConvos, 1000);

function removeFriend(id){
  let param = `friendId=${id}`;
  ajaxCalls("POST", "removeFriendAjax.php", param).then(function(result) {
    var data = JSON.parse(result);
    console.log("Response messageSimple : " + data);
    document.querySelector(".friends-list-elements").innerHTML = "";
    console.log(data.length);
    for (i = 0; i < data.length; i++) {
      var obj = data[i];
      var friend = `
        <div class='friend'>
          <div class='friend-image'>
            <img class='post-avatar post-avatar-30' src='${obj.profile_pic}'  >
          </div>
          <div class='friend-info'>
            <a href="timeline.php?visitingUserID=${obj.user_id}" class='friend-text'>${obj.name}</a>            
          </div>
          <div class='friend-action'>&nbsp&nbsp&nbsp
            <a href="javascript:removeFriend(${obj.user_id})" class='remove-friend'><i class="fas fa-times"></i></a>
          </div>
        </div>  
      `;
        document.querySelector(".friends-list-elements").innerHTML += friend;
      }
  });
}