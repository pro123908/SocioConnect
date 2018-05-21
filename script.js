function showCommentField(id) {
  // Displaying comment section when comment button is clicked
  document.getElementById("post_id_" + id).classList.toggle("hidden");
}

function like(postID) {
  // Function for adding a like

  // Creating XHR object for AJAX Call
  var xmlhttp = new XMLHttpRequest();

  // If response has arrived for request made
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xmlhttp.status == 200) {
        // Adding response returned, like count in this case to post
        document.querySelector(
          `.likeCount-${postID}`
        ).textContent = this.responseText.trim();
      } else if (xmlhttp.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  // Preparing like.php with postID for data
  xmlhttp.open("GET", `like.php?like=${postID}`, true);

  // Sending request
  xmlhttp.send();
}

function comment(postID) {
  // Adding comment to post

  // Creating XHR object for AJAX Call
  var xmlhttp = new XMLHttpRequest();

  // Getting post ID,content and user who posted comment from form
  var post = document.querySelector(`input[name='post_id_${postID}']`);
  var comment = document.querySelector(`input[name='comment_${post.value}']`);
  var user = document.querySelector('input[name="post_user"]');
  console.log(post);
  console.log(comment);

  // Setting up parameters for POST request to the file
  var param = `comment=${comment.value}&post_id=${post.value}`;

  // If response has arrived for request made
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xmlhttp.status == 200) {
        // Added Comment ID is returned in response
        commentID = this.responseText.trim();

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
      } else if (xmlhttp.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  // Preparing request
  xmlhttp.open("POST", "comment.php", true);

  // Setting up headers to be send in POST request
  xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  // Sending the request along the POST parameters
  xmlhttp.send(param);

  // Pain in the ass xD
  return false;
}

function deletePost(postID) {
  // As the name suggests

  // Creating XHR object for AJAX Call
  var xmlhttp = new XMLHttpRequest();

  // If response has arrived
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xmlhttp.status == 200) {
        // Hiding the post to fake deletion xD
        document.querySelector(`.post_${postID}`).style.display = "none";
      } else if (xmlhttp.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  // Preparing for GET request to delete.php
  xmlhttp.open("GET", `delete.php?id=${postID}`, true);

  // Same drill xD
  xmlhttp.send();
}

function addPost(user_id) {
  // Again the name suggests xD

  // Creating XHR object for AJAX Call
  var xhr = new XMLHttpRequest();

  // Getting post content
  var post = document.querySelector("textarea[name='post']");

  // Setting paramters for POST request
  var param = `post=${post.value}&user_id=${user_id}`;

  // When response has arrived
  xhr.onreadystatechange = function() {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xhr.status == 200) {
        // Adding new post to post Area
        // Adding post to the top not bottom. Clue xD
        document.querySelector("#postArea").innerHTML =
          this.responseText + document.querySelector("#postArea").innerHTML;
      } else if (xhr.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  // Preparing POST request
  xhr.open("POST", "post.php", true);

  // Setting up headers
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  //Sending the request with parameters
  xhr.send(param);
}

function deleteComment(commentID) {
  // Deleting Comment specified by comment ID

  // Creating XHR object for AJAX Call
  var xmlhttp = new XMLHttpRequest();

  // When response has arrived
  xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xmlhttp.status == 200) {
        // Faking the deletion of comment
        document.querySelector(`.comment_${commentID}`).style.display = "none";
      } else if (xmlhttp.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  // Preparing the request
  xmlhttp.open("GET", `commentDelete.php?id=${commentID}`, true);

  // Sending the request
  xmlhttp.send();
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

function getUsers(value, user_id) {
  // Creating XHR object for AJAX Call
  var xhr = new XMLHttpRequest();

  // Setting paramters for POST request
  var param = `query=${value}&user_id=${user_id}`;

  // When response has arrived
  xhr.onreadystatechange = function() {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xhr.status == 200) {
          // Displaying search results 
        document.querySelector(".search_results").innerHTML = this.responseText;
      } else if (xhr.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  //Preparing the request
  xhr.open("POST", "search.php", true);
  
  // Setting up headers
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

  // Sending paramters with request
  xhr.send(param);

  // Pain in the ass
  return false;
}


setInterval(pageRefresh,2000);

function pageRefresh(){
  // Creating XHR object for AJAX Call
  var xhr = new XMLHttpRequest();


  // When response has arrived
  xhr.onreadystatechange = function() {
    if (xhr.readyState == XMLHttpRequest.DONE) {
      // XMLHttpRequest.DONE == 4
      if (xhr.status == 200) {
          // Displaying search results 
        
      } else if (xhr.status == 400) {
        alert("There was an error 400");
      } else {
        alert("something else other than 200 was returned");
      }
    }
  };

  //Preparing the request
  xhr.open("GET", "main.php", true);

  // Sending paramters with request
  xhr.send();
}