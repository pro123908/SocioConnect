function setUserId(id) {
  var url = window.location.href;
  if(url == 'http://localhost/socioConnect/main.php' || url == 'http://localhost/socioConnect/timeline.php' || url.slice(0,42) == 'http://localhost/socioConnect/timeline.php'){
    var post = document.querySelectorAll(".post");
    //If there are no posts
    if(post.length == 0)
      document.getElementById("loading").innerHTML = 'No Posts To Show'; 
    //If there are less than 10 posts     
    else if(post.length < 10)
      document.getElementById("loading").innerHTML = 'No More Posts To Show';
    //If there are 10 posts       
    else{
      var flag = document.getElementById("noMorePosts");
      //if no more flag is true
      if(flag.value == "true")
          document.getElementById("loading").innerHTML = 'No More Posts To Show';
      //if there are more posts present    
      else{
        if(url == 'http://localhost/socioConnect/timeline.php'){
           var loading = `<a href="javascript:showNextPage('b')">Show More Posts</a>`; 
        }
        else if(url.slice(0,42) == 'http://localhost/socioConnect/timeline.php'){
          var id = url.slice(58)
          var loading = `<a href="javascript:showNextPage('${id}')">Show More Posts</a>`;
        }
        else{
          var loading = `<a href="javascript:showNextPage('a')">Show More Posts</a>`; 
        }
        document.getElementById("loading").innerHTML = loading;
      }

          // <div id='loading-messages' class='loading-messages'><a href="javascript:showNextPageMessages('<?php echo $_GET['id']?>')">Show More Messages</a></div>
    }    
  }
  else if(url.slice(0,42) == 'http://localhost/socioConnect/messages.php'){
    var msgs = document.querySelectorAll(".chat-message");
    if(msgs.length == 0)
      document.getElementById("loading-messages").innerHTML = 'No Messages To Show';
    else if(msgs.length < 10)
      document.getElementById("loading-messages").innerHTML = 'No More Messages To Show';    
    else{
      var flag = document.getElementById("noMoreMessages");
      if(flag.value == "true")
          document.getElementById("loading-messages").innerHTML = 'No More Messages To Show';
      else{
          var id = url.slice(46)
          if(id > 0){
            document.getElementById("loading-messages").innerHTML = `<a href="javascript:showNextPageMessages('${id}')">Show More Messages</a>`
          }
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
    ).innerHTML = `<i class='like-count-icon fas fa-thumbs-up'></i> ${value}`;
    let icon = document.querySelector(`.post-${postID} .like-btn i`);
    icon.classList.toggle("blue");
    //Adding in recent activities if liked 
    if(icon.classList[2]){
      var activity_type = 0;
      param = `target_id=${postID}&activity_type=${activity_type}`;
      ajaxCalls("POST", `recentActivityAjax.php`,param).then(function(result) {
        addRecentActivity(result);
      });    
    }
    else{
      var activity_type = 4;
      param = `target_id=${postID}&activity_type=${activity_type}`;
      ajaxCalls("POST", `recentActivityAjax.php`,param).then(function(result) {
        document.querySelector(".activities-content").innerHTML = result;
      });
    }
  });
}

function addRecentActivity(activity){
  var activitiesDiv = document.querySelector(".activities-content");
  alert(activitiesDiv.childNodes.length);
  if(activitiesDiv.childNodes.length == 21){
    //Here write the code to remove the last child in the div
  }
  activitiesDiv.innerHTML = activity + activitiesDiv.innerHTML;
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
  `;

    comment.value = "";
    console.log(document.querySelector(`.comment-count-${postID}`));

    //Adding in recent activities
    var activity_type = 1;
    var commentDetails = postID + " " + commentID;
    param = `target_id=${commentDetails}&activity_type=${activity_type}`;
    ajaxCalls("POST", `recentActivityAjax.php`,param).then(function(result) {
      addRecentActivity(result);
    });
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
    document.querySelector("textarea[name='post']").value = " ";

    //Adding in recent activities
    var activity_type = 2;
    param = `activity_type=${activity_type}`;
    ajaxCalls("POST", `recentActivityAjax.php`,param).then(function(result) {
      addRecentActivity(result);
    });
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
  // Setting paramters for POST request
  var param = `query=${value}&flag=${flag}`;
  var searchFooter;
  ajaxCalls("POST", "search.php", param).then(function(result) {
    if (result == "No") {
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
          //   searchFooter = `<form method="GET" action="allSearchResults.php"><input type="hidden" name="query" value=${value}><input type="submit" value="View All Results For ${value}"></form>`;
          // document.querySelector(
          //   ".search-result"
          // ).innerHTML += searchFooter;

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
    document.querySelector(`.like-count-${postID} .count`).innerHTML = "";
    var data = JSON.parse(result);
    let flag = true;

    for (i = 0; i < data.length; i++) {
      flag = false;
      var obj = data[i];
      console.log("In");
      document.querySelector(`.like-count-${postID} .count`).innerHTML += `${
        obj.name
      }<br>`;
    }
    if (flag) {
      document
        .querySelector(`.like-count-${postID} .count`)
        .classList.remove("tooltip");
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
  
  var width = document.querySelector('.chat-messages').offsetWidth;
  var widthX = document.querySelector('.chat-messages .message');

  console.log('width : ' + width*0.8);
  console.log(messageBody.value.length);
  if(messageBody.value.length > 0){
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
      if(document.getElementById("loading-messages").innerHTML == 'No Messages To Show')
        document.getElementById("loading-messages").innerHTML = 'No More Messages To Show';    
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
        if (document.querySelector(".recent-user-" + obj.fromID)){
          document.querySelector(".recent-user-" + obj.fromID).style.display =
            "none";
        }
        var recentMessage = `
        <a href='messages.php?id=${obj.fromID}' class='recent-user recent-user-${obj.fromID}'>
          <span class='recent-user-image'>
            <img src='${obj.pic}' class='post-avatar post-avatar-40' />
          </span>
          <span class='recent-message-info'>
            <span class="recent-username">${obj.partner}</span>
            <span class='recent-message-text'>${obj.from} ${obj.msg}</span>
            <span class='recent-message-time'>${obj.at}</span>
          </span>
          <i class='tooltip-container far fa-trash-alt  comment-delete' onclick='javascript:deleteConvo(${obj.fromID})'><span class='tooltip tooltip-left'>Delete</span></i>
        </a>
        `;
        if(document.querySelector(".recent-chats")){
        document.querySelector(".recent-chats").innerHTML =
          recentMessage + document.querySelector(".recent-chats").innerHTML;
        }
      }
    }
  });
}
setInterval(refreshRecentConvos, 1000);

function deleteConvo(id){
  var url = window.location.href;
  var openConvoId =  url.slice(46);
  let param = `id=${id}&urlID=${openConvoId}`;  
  ajaxCalls("POST", `deleteConvoAjax.php`,param).then(function(response) {
    console.log(response);
      //If response is not a redirection, this would be changed if this comment is removed from messags.php
      if(response == "Reload the page")
        window.location = "messages.php";
      else
        document.querySelector(".recent-chats").innerHTML = response;
  });
}

function showPageMessages(id,page){
  document.getElementById("loading-messages").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET","loadMessagesAjax.php?id="+id+"&page="+page,true);
  xhr.onload = function(){
    if(this.status = 200){
      document.querySelector(".chat-messages").innerHTML = this.responseText + document.querySelector(".chat-messages").innerHTML;
      document.getElementById("loading-messages").style.display = 'block';
    }
    if(document.getElementById("noMoreMessages").value == "true")
      document.getElementById("loading-messages").innerHTML = 'No More Messages To Show';
  }
  xhr.send();
} 

function showNextPageMessages(id){
  var noMorePosts = document.getElementById("noMoreMessages");
  var page = document.getElementById("nextPageMessages");
  if (noMorePosts.value == "false") {
    //deleting previous data
    var div = document.querySelector(".chat-messages");
    div.removeChild(page);
    div.removeChild(noMorePosts);

    showPageMessages(id, page.value);
  } 
  else {
    alert("khtm");
  }
}

function removeFriend(id) {
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
            <a href="timeline.php?visitingUserID=${
              obj.user_id
            }" class='friend-text'>${obj.name}</a>            
          </div>
          <div class='friend-action'>&nbsp&nbsp&nbsp
            <a href="javascript:removeFriend(${
              obj.user_id
            })" class='remove-friend'><i class="fas fa-times"></i></a>
          </div>
        </div>  
      `;
      document.querySelector(".friends-list-elements").innerHTML += friend;
    }
  });
}

window.onclick = function(e) {
  if (e.srcElement.className != "search-input") {
    document.querySelector(".search-result").style.display = "none";
  }
}

function showPage(flag, page) {
  document.getElementById("loading").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET",`loadPostsAjax.php?flag=${flag}&page=${page}`,true);
  xhr.onload = function(){
    if(this.status = 200){
      document.querySelector(".posts").innerHTML += this.responseText;
      document.getElementById("loading").style.display = 'block';
    }
    if(document.getElementById("noMorePosts").value == "true")
      document.getElementById("loading").innerHTML = 'No More Posts To Show';
  }
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

function hello(){
  alert("hello");
}


function notificationDropdown(){
  
  let display = document.querySelector('.noti-dropdown').style.display;

  if(display == 'block'){
    document.querySelector('.noti-dropdown').style.display = 'none';
  }else{
    document.querySelector('.noti-dropdown').style.display = 'block';
  }
}


