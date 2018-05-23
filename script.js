function showCommentField(id) {
  // Displaying comment section when comment button is clicked
  document.getElementById("post_id_" + id).classList.toggle("hidden");
}

function like(postID) {
  ajaxCalls("GET", `like.php?like=${postID}`).then(function(result) {
    document.querySelector(`.likeCount-${postID}`).textContent = result.trim();
  });
}

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
          alert("something else other than 200 was returned");
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

  // Setting up parameters for POST request to the file
  var param = `comment=${comment.value}&post_id=${post.value}`;

  ajaxCalls("POST", "comment.php", param).then(function(result) {
    // Added Comment ID is returned in response
    commentID = result.trim();

    // Rendering the added comment in post
    document.querySelector(`.commentArea_${post.value}`).innerHTML += `
        <div class='comment comment_${commentID}'>
        <a class='commentDelete' href="javascript:deleteComment(${commentID})">X</a>
          <span class='commentUser'>${user.value} : </span>
          <span class='commentText'>${comment.value}</span>
          <span class='commentTime'>1 Second Ago</span>
        </div>
   `;
    comment.value = "";
  });

  // Pain in the ass xD
  return false;
}

function deletePost(postID) {
  // As the name suggests

  ajaxCalls("GET", `delete.php?id=${postID}`).then(function(result) {
    document.querySelector(`.post_${postID}`).style.display = "none";
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
    document.querySelector("#postArea").innerHTML =
      result + document.querySelector("#postArea").innerHTML;
  });
}

function deleteComment(commentID) {
  // Deleting Comment specified by comment ID

  ajaxCalls("GET", `commentDelete.php?id=${commentID}`).then(function(result) {
    document.querySelector(`.comment_${commentID}`).style.display = "none";
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

function getUsers(value) {
  // Setting paramters for POST request
  var param = `query=${value}`;

  ajaxCalls("POST", "search.php", param).then(function(result) {
    // Displaying search results
    document.querySelector(".search_results").innerHTML = result;
  });
  // Pain in the ass
  return false;
}

function getUsersForMessages(value) {
  // Setting paramters for POST request
  var param = `query=${value}`;

  ajaxCalls("POST", "search.php", param).then(function(result) {
    // Displaying search results
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
         <div class='comment comment_${obj.postID}'>
         <span class='commentUser'>${obj.name} : </span>
         <span class='commentText'>${obj.comment}</span>
         <span class='commentTime'>Just now</span>
     </div>
         `;

      document.querySelector(`.commentArea_${obj.postID}`).innerHTML += comment;
    }
  });
}

setInterval(notificationRefresh, 1000);

function notificationRefresh() {
  ajaxCalls("GET", "notificationsAjax.php").then(function(result) {
    // Displaying search results
    var data = JSON.parse(result);
    for (i = 0; i < data.length; i++) {
      var obj = data[i];

      var notification = `
       <a href='notification.php?postID=${obj.postID}&type=${obj.type}&notiID=${
        obj.notiID
      }'>${obj.name} has ${obj.type} your post<br><br></a>
        `;

      document.querySelector(`.notifications`).innerHTML += notification;
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

      document.querySelector(`.likeCount-${obj.postID}`).innerHTML = obj.likes;
    }
  });
}

function likeUsers(postID) {
  
  ajaxCalls("GET", `likeUsers.php?postID=${postID}`).then(function(result) {
    // Displaying search results
    var data = JSON.parse(result);

    for (i = 0; i < data.length; i++) {
      var obj = data[i];

      document.querySelector(`.likeUsers-${postID}`).innerHTML +=
        " " + obj.name + " " + "|";
    }
  });
}

function hideLikers(postID) {
  // Hiding likers when clicked on number again
  document.querySelector(`.likeUsers-${postID}`).innerHTML = "";
}
