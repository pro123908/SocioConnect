function setUserId(userLoggedInId) {
  var path = window.location.pathname; //"/socioConnect/messages.php"
  var args = window.location.search; //"?id=32"

  //to get id from url, slicing the url after "=" sign
  var id = args.slice(args.search("=") + 1);

  if (
    path == "/socioConnect/main.php" ||
    path == "/socioConnect/timeline.php" ||
    path == "/main.php" ||
    path == "/timeline.php"
  ) {
    var post = document.querySelectorAll(".post"); // Selecting all posts on page

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
        if (
          (!id && path == "/socioConnect/timeline.php") ||
          path == "/timeline.php"
        ) {
          // If it is your timeline
          var loading = `<a href="javascript:showNextPage('b')">Show More Posts</a>`;
        } else if (id) {
          // If it is someone else timeline
          var loading = `<a href="javascript:showNextPage('${id}')">Show More Posts</a>`;
        } else {
          // Not sure about this? ------------------------
          var loading = `<a href="javascript:showNextPage('a')">Show More Posts</a>`;
        }
        document.getElementById("loading").innerHTML = loading;

        if (path == "/socioConnect/timeline.php" || path == "/timeline.php") {
          var recenetUploads = document.querySelectorAll(".recent-uploads");
          if (recenetUploads.length == 0)
            document.querySelector(".recent-uploads-footer").innerHTML =
            "No Recent Uploads";
        }
      }
    }
  } else if (path == "/socioConnect/messages.php" || path == "/messages.php") {
    // Selecting all messages on the page
    var msgs = document.querySelectorAll(".chat-message");

    if (msgs.length == 0)
      // If no messages
      document.getElementById("loading-messages").innerHTML =
      "No Messages To Show";
    else if (msgs.length < 10)
      // If not more than 10
      document.getElementById("loading-messages").innerHTML =
      "No More Messages To Show";
    else {
      // messages are not less than 10
      var flag = document.getElementById("noMoreMessages");
      if (flag.value == "true")
        // no more messages are there
        document.getElementById("loading-messages").innerHTML =
        "No More Messages To Show";
      else {
        // more messages are there
        if (id) {
          document.getElementById(
            "loading-messages"
          ).innerHTML = `<a href="javascript:showNextPageMessages('${id}')">Show More Messages</a>`;
        }
      }
    }
  } else if (
    path == "/socioConnect/allNotification.php" ||
    path == "/allNotification.php"
  ) {
    // Selecting all notifications on page
    var notis = document.querySelectorAll(".notification");
    if (notis.length == 0)
      // If no notis
      document.getElementById("loading-notis").innerHTML =
      "No Notifications To Show";
    else if (notis.length < 10)
      // if less than 10 notis
      document.getElementById("loading-notis").innerHTML =
      "No More Notifications To Show";
    else {
      // notis are not less than 10
      var flag = document.getElementById("noMoreNotis");
      if (flag.value == "true")
        // Not greater than 10 notis
        document.getElementById("loading-notis").innerHTML =
        "No More Messages To Show";
      else {
        // There are more notis more than 10
        document.getElementById(
          "loading-notis"
        ).innerHTML = `<a href="javascript:showNextPageNotis()">Show More Notifications</a>`;
      }
    }
  } else if (
    path == "/socioConnect/allActivities.php" ||
    path == "/allActivities.php"
  ) {
    // Selecting all recent activities on page
    var notis = document.querySelectorAll(".recent_activity ");
    if (notis.length == 0)
      // If no recent activity
      document.getElementById("loading-activities").innerHTML =
      "No Activities To Show";
    else if (notis.length < 10)
      // if less than 10
      document.getElementById("loading-activities").innerHTML =
      "No More Activities To Show";
    else {
      // if not less than 10
      var flag = document.getElementById("noMoreActivities");
      if (flag.value == "true")
        // if equal to 10 only
        document.getElementById("loading-activities").innerHTML =
        "No More Activities To Show";
      else {
        // more activities are there to render
        document.getElementById(
          "loading-activities"
        ).innerHTML = `<a href="javascript:showNextPageActivities()">Show More Activities</a>`;
      }
    }
  }
  session_user_id = userLoggedInId; // No purpose yet
}

function showCommentField(id) {
  // Displaying comment section when comment button is clicked
  document.getElementById("comment-section-" + id).classList.toggle("hidden");
}

function like(postID) {
  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX3.php?like=${postID}`).then(
    function (result) {
      let value = result.trim(); // because there is a space in response (don't know why))

      // Displaying like count on post
      document.querySelector(
        `.like-count-${postID}`
      ).innerHTML = `<i class='like-count-icon far fa-thumbs-up'></i> ${value}<span class='tooltip tooltip-bottom count'></span>`;

      // Changing state of like icon
      let icon = document.querySelector(`.post-${postID} .like-btn i`);
      icon.classList.toggle("blue");

      //Adding in recent activities if liked
      if (icon.classList[2]) {
        // If it has a class blue xD
        var activity_type = 0; // Activity - like

        // Ajax call for adding like activity in recent activity
        param = `target_id=${postID}&activity_type=${activity_type}`;
        ajaxCalls(
          "POST",
          `./includes/AjaxHandlers/AJAX2.php?recentActivity=1`,
          param
        ).then(function (result) {
          // Adding to the view
          addRecentActivity(result);
        });
      } else {
        // Means post has been disliked
        var activity_type = 4; // Unlike
        param = `target_id=${postID}&activity_type=${activity_type}`;

        ajaxCalls(
          "POST",
          `./includes/AjaxHandlers/AJAX2.php?recentActivity=1`,
          param
        ).then(function (result) {
          document.querySelector(".activities-content").innerHTML = result;
        });
      }
    }
  );
}

function addRecentActivity(activity) {
  //Adding recent activity to the activity area
  // Getting the area
  if (
    window.location.pathname == "/socioConnect/main.php" ||
    window.location.pathname == "/main.php"
  ) {
    var activitiesDiv = document.querySelector(".activities-content");

    // Inserting the new activity at the top
    activitiesDiv.innerHTML = activity + activitiesDiv.innerHTML;

    // If activities have become more than 10 , then just remove the bottom one from view
    if (findChildNodes(activitiesDiv) == 11) {
      document.querySelector(".show-more-activities").innerHTML =
        "<a href='allActivities.php' class='see-more'><span>See more</span></a>";

      //Removing bottom activity from the list
      var lastChild = activitiesDiv.getElementsByTagName("a")[10]; // 0 -> 9 are 10
      var removed = activitiesDiv.removeChild(lastChild); // returned the removed element
    } else if (findChildNodes(activitiesDiv) > 0) {
      // If activities were not more than 10
      document.querySelector(".show-more-activities").innerHTML =
        "<p class='see-more'>No More Activities to Show</p>";
    }
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

// Function for making all ajax calls using promise
function ajaxCalls(method, pathString, postParam = "", pic = "") {
  // Creating promise
  return new Promise(function (resolve, reject) {
    // Creating XHR object for AJAX Call
    var xmlhttp = new XMLHttpRequest();

    // If response has arrived for request made
    xmlhttp.onreadystatechange = function () {
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

    // If it is post request and pic is not attached
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
  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX2.php?canComment=1`).then(function (result) {
    if (result) {
      // Adding comment to post

      // Getting targeted comemnt field
      var comment = document.querySelector(`input[name='comment_${postID}']`);

      // Extracting Value
      var commentValue = comment.value;

      var timeToShow = "Just Now";

      // Validating input to not to be empty
      if (!(commentValue.trim() == "")) {
        // trim() - removes white spaces leading and trailing (for empty comment)

        // Setting up parameters for POST request to the file
        var param = `comment=${comment.value}&post_id=${postID}`;

        ajaxCalls(
          "POST",
          "./includes/AjaxHandlers/AJAX3.php?comment=1",
          param
        ).then(function (result) {
          // Added Comment ID is returned in response
          commentID = result.trim();

          document.querySelector(`.comment-area-${postID}`).innerHTML += `
      <div class='comment comment-${commentID}'>
                    
      <div class='user-image'>
          <img src='${profilePic}' class='post-avatar post-avatar-30' />
      </div>
      
      <div class='comment-info'>
      <i class='tooltip-container fas fa-times comment-delete' onclick='javascript:deleteComment(${commentID})'><span class='tooltip tooltip-right'>Remove</span></i>
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
          var activity_type = 1;
          // targeted Content
          var commentDetails = postID + " " + commentID;
          param = `target_id=${commentDetails}&activity_type=${activity_type}`;
          ajaxCalls(
            "POST",
            `./includes/AjaxHandlers/AJAX2.php?recentActivity=1`,
            param
          ).then(function (result) {
            addRecentActivity(result);
          });
        });
      }
    } else {
      alert("You've reached your limit of number of comments allowed for a single account");
    }
  });
  // Pain in the ass xD
  return false;
}

function deletePost(postID) {
  // As the name suggests

  ajaxCalls(
    "GET",
    `./includes/AjaxHandlers/AJAX3.php?deletePost=1&id=${postID}`
  ).then(function (result) {
    // Removing post from the view
    document.querySelector(`.post-${postID}`).style.display = "none";
    if (
      window.location.pathname == "/socioConnect/timeline.php" ||
      window.location.pathname == "/timeline.php"
    ) {
      ajaxCalls(
        "GET",
        `./includes/AjaxHandlers/AJAX2.php?refreshRecentUploads=1`
      ).then(function (result) {
        document.querySelector(".recenet-uploads-content").innerHTML = result;
        var recenetUploads = document.querySelectorAll(".recent-uploads");
        if (recenetUploads.length == 0)
          document.querySelector(".recent-uploads-footer").innerHTML =
          "No Recent Uploads";
      });
    }
  });
}

function addPost(user_id) {
  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX2.php?canPost=${user_id}`).then(function (result) {
    if (result) {
      // Again the name suggests xD

      // Getting post content
      var post = document.querySelector("textarea[name='post']"); // Post
      var postPicData = document.querySelector("input[name='post-pic']"); // Post Pic
      var postPic = postPicData.files[0];



      var postContent = post.value; // Post text

      //Validating post content before uploading
      if (!(postContent.trim() == "") || postPic !== undefined) {
        // Making up data for sending along request
        var formData = new FormData();
        formData.append("file", postPic); // Picture
        formData.append("post", post.value);


        ajaxCalls(
          "POST",
          "./includes/AjaxHandlers/AJAX3.php?post=1",
          formData,
          "pic"
        ).then(function (result) {
          // Adding new post to post Area
          // Adding post to the top not bottom. Clue xD

          if (result == 1 || result == 2) {
            if (result == 1) {
              document.querySelector('.pic-name').innerHTML = "File Too Large";
              // alert("File Too Large");
            } else if (result == 2) {
              document.querySelector('.pic-name').innerHTML = "Incomaptible File";
              // alert("Incomaptible File");
            }

            postPicData = "";
            postPicData.files[0] = "";
          } else {
            document.querySelector(".posts").innerHTML =
              result + document.querySelector(".posts").innerHTML;

            // Clearing the post text from new post area when it is posted
            document.querySelector("textarea[name='post']").value = " ";

            //Adding in recent activities
            var activity_type = 2;
            param = `activity_type=${activity_type}`;
            ajaxCalls(
              "POST",
              `./includes/AjaxHandlers/AJAX2.php?recentActivity=1`,
              param
            ).then(function (result) {
              if (
                window.location.pathname == "/socioConnect/timeline.php" ||
                window.location.pathname == "/timeline.php"
              ) {
                if (postPic !== undefined) {
                  ajaxCalls(
                    "GET",
                    `./includes/AjaxHandlers/AJAX2.php?refreshRecentUploads=1`
                  ).then(function (result) {
                    document.querySelector(
                      ".recenet-uploads-content"
                    ).innerHTML = result;
                  });

                  //Unsetting the no recent uploads message
                  document.querySelector(".recent-uploads-footer").innerHTML = "";
                }
              } else addRecentActivity(result);
            });
          }
        });
      }

      // Clearing the name of pic
      document.querySelector(".pic-name").innerHTML = "";
    } else {
      alert("You've reached your limit of number of posts allowed for a single account");
    }
  });
}

function hideEditDiv(postID, flag) {
  // Getting the parent DIV of actual post
  var parentDiv = document.querySelector(".post-content-" + postID);

  // Displaying actual post content
  document.querySelector(".actual-post-" + postID).style.display = "block";

  // Removing the edit post div from the view
  parentDiv.removeChild(document.querySelector(".edit-post-" + postID));

  // Displaying 'edited' text if post has been edited
  if (flag) document.querySelector(".post-edited-" + postID).innerHTML = "";
}

function postPicSelected(container) {
  // For displaying name of the file which is selected when making post
  var postPic = document.querySelector("input[name='post-pic']").files[0];
  document.querySelector(".pic-name").innerHTML = postPic.name;
}

//Copied this function from above postPicSelected, just to check right now, in future both will be merged
function editedPostPicSelected(postID) {
  // Displaying selected pic name in edited mode
  var editForm = document.querySelector(".edit-post-" + postID);
  var postPic = editForm.querySelector("input[name='post-pic']").files[0];
  editForm.querySelector(".pic-name").innerHTML = postPic.name;
}

function showFileUpload(postID) {
  // Showing file upload button
  var editForm = document.querySelector(".edit-post-" + postID);
  editForm.querySelector(".upload-btn-wrapper").style.display = "inline-block";
}

function hideFileUpload(postID) {
  // Hiding file upload button
  var editForm = document.querySelector(".edit-post-" + postID);
  editForm.querySelector(".upload-btn-wrapper").style.display = "none";
}

function editPost(postID) {
  // if edit post div is not opened
  if (!document.querySelector(".edit-post-" + postID)) {
    //Current Post
    var post = document.querySelector(".actual-post-" + postID);

    //Current Post and picture
    var postPic = post.querySelector(".post-image-container");
    var postContent = post.getElementsByTagName("p")[0];

    //Hiding current post
    post.classList.toggle("hidden");

    //Creating a new div to display if it doesn't exist and get the edit input and inserting it in the same parent div, just before the div where text and pic were shown

    var div = document.createElement("div");
    div.setAttribute("class", "show edit-post edit-post-" + postID);

    var videoDiv = post.querySelector(".post-video");
    var imgDiv = post.querySelector(".post-image");
    var conflict =
      '<label><input type="radio" name="edit-post-pic" value="keep" onclick="hideFileUpload(${postID})"> No Photo/Video</label><br>';
    var flag = false;
    if (imgDiv) {
      if (imgDiv.style.display != "none") flag = true;
    }
    if (videoDiv) {
      if (videoDiv.style.display != "none") flag = true;
    }
    if (flag) {
      conflict = `<label><input type="radio" name="edit-post-pic" value="keep" onclick="hideFileUpload(${postID})"> Keep Current Photo/Video</label><br>
      <label><input type="radio" name="edit-post-pic" value="remove" onclick="hideFileUpload(${postID})"> Remove Current Photo/Video</label><br>
    `;
    }

    div.innerHTML = `<form action="" method='POST'>
          <textarea name="post" id="" cols="30" rows="10" class="post-input post-edit-${postID}">${
      postContent.innerHTML
    }</textarea>
          <br>
          <div class ="radio-buttons-edit">
            ${conflict}
            <label><input type="radio" name="edit-post-pic" value="new" onclick="showFileUpload(${postID})"> Upload New Photo/Video</label><br>
          </div> 
          <div class='upload-btn-wrapper' style="display:none;">
            <button class='pic-upload-btn'><i class='far fa-image'></i></button>
            <input type='file' name='post-pic' onchange='javascript:editedPostPicSelected(${postID})'  />
            <span class='pic-name'></span>
          </div>               
          <div class='edit-post-button-container'>
            <a  href="javascript:saveEditPost(${postID})"  class='edit-post-save-btn'>Save</a>
            <a  href="javascript:hideEditDiv(${postID},false)"  class='edit-post-cancel-btn'>Cancel</a>
          </div>
        </form>`;

    var parentDivForEditingArea = document.querySelector(
      ".post-content-" + postID
    );

    // Adding edit post div before actual post din in post content div
    parentDivForEditingArea.insertBefore(div, post);
  } else {
    // If edited div is already there then hide it and show actual post
    document.querySelector(".edit-post-" + postID).classList.toggle("hidden");
    document.querySelector(".actual-post-" + postID).classList.toggle("hidden");
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

  //If neither text nor pic was inserted
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
        alert("Select File to update media");
        return 0;
      }

      //Preparing formData for ajax call
      var formData = new FormData();
      formData.append("file", postPic);
      formData.append("postID", postID);
      formData.append("postContent", postContent.value);
      formData.append("action", action);

      ajaxCalls(
        "POST",
        "./includes/AjaxHandlers/AJAX3.php?editPost=1",
        formData,
        "pic"
      ).then(function (result) {
        //Displaying original post div which was made hidden in previous function
        var post = document.querySelector(".actual-post-" + postID);
        //Storing edited status in the p tag
        post.getElementsByTagName("p")[0].innerHTML = postContent.value;

        //result CONTAINS THE PATH OF IMAGE
        //checking if the response path is empty, i.e no image is to be shown
        var videoDiv = post.querySelector(".post-video");
        var imgDiv = post.querySelector(".post-image");

        var path = postPicData.value;
        if (result.trim() != "") {
          if (path == "") path = result;
          var extension = path.slice(path.indexOf(".") + 1);
          if (extension == "mp4" || extension == "mp4" || extension == "mp4") {
            if (imgDiv) imgDiv.style.display = "none";
            if (videoDiv) {
              videoDiv.style.display = "block";
              videoDiv.src = "./assets/post_videos/" + result;
            }

            //if div isn't present then creating it from scratch
            else {
              var videoParentDiv = document.querySelector(
                ".actual-post-" + postID
              );
              videoParentDiv.innerHTML += `<div class='post-image-container'><video class='post-video' controls><source src="./assets/post_videos/${result}"></video></div>`;
            }
          } else {
            if (videoDiv) videoDiv.style.display = "none";
            if (imgDiv) {
              imgDiv.style.display = "block";
              imgDiv.src = "./assets/post_pics/" + result;
            }

            //if div isn't present then creating it from scratch
            else {
              var imgParentDiv = document.querySelector(
                ".actual-post-" + postID
              );
              imgParentDiv.innerHTML += `<div class='post-image-container'><img src='./assets/post_pics/${result}' class='post-image' /></div>`;
            }
          }
          // div for image is already present then only updating its src, making display block bcozit might be made none due to line 443 (See if condition in the else block)
        } else {
          //response was empty this means that there is no picture to show, so first check, if div for images is present then hide it
          if (imgDiv || videoDiv) {
            if (imgDiv) imgDiv.style.display = "none";
            if (videoDiv) videoDiv.style.display = "none";
            if (
              window.location.pathname == "/socioConnect/timeline.php" ||
              window.location.pathname == "/timeline.php"
            ) {
              ajaxCalls(
                "GET",
                `./includes/AjaxHandlers/AJAX2.php?refreshRecentUploads=1`
              ).then(function (result) {
                document.querySelector(
                  ".recenet-uploads-content"
                ).innerHTML = result;
                var recenetUploads = document.querySelectorAll(
                  ".recent-uploads"
                );
                if (recenetUploads.length == 0)
                  document.querySelector(".recent-uploads-footer").innerHTML =
                  "No Recent Uploads";
              });
            }
          }
        }

        //Now hide the editing div and write Edited in the header section of the post
        hideEditDiv(postID, true);

        //Write Edited on the post
        document.querySelector(`.post-edited-${postID}`).innerHTML = "Edited";
      });
    } else alert("Select action to do with the media");
  } else {
    alert("Enter either a text or a media file");
  }
}

function deleteComment(commentID) {
  // Deleting Comment specified by comment ID

  ajaxCalls(
    "GET",
    `./includes/AjaxHandlers/AJAX3.php?deleteComment=1&id=${commentID}`
  ).then(function (result) {
    // taking comment out from view
    document.querySelector(`.comment-${commentID}`).style.display = "none";
  });
}

function saveEditComment(postID, commentID, user, profilePic, time) {
  // Adding comment to post

  // Getting post ID,content and user who posted comment from form

  // Getting comment value (edited)
  var comment = document.querySelector(
    `input[name='comment_edit_${commentID}']`
  );

  // Setting up parameters for POST request to the file
  var param = `comment=${comment.value}&comment_id=${commentID}`;

  //Showing post comment form
  // document.querySelector(`.comment-form-${postID}`).style.display = 'flex';

  ajaxCalls("POST", "./includes/AjaxHandlers/AJAX3.php?editComment=1", param)
    .then(function (result) {
      showComment(
        user,
        commentID,
        postID,
        profilePic,
        time,
        comment.value,
        true
      );
    })
    .catch(function (reject) {
      // console.log("REJECTED");
    });

  return false;
}

function editComment(commentID, postID, profilePic, time) {
  // For displaying edit comment form

  // If edited comment form is not already opened
  if (!document.querySelector(`.edit-comment-form-${postID}`)) {
    var comment = document.querySelector(".comment-" + commentID);

    var commentSection = document.querySelector(`#comment-section-${postID}`);

    var user = comment.querySelector(".comment-user").innerHTML;
    user = user.slice(0, user.length - 3);
    var commentValue = comment.querySelector(".comment-text").innerHTML;
    var currentComment = comment.innerHTML;

    // Hiding comment form
    document.querySelector(`.comment-form-${postID}`).style.display = "none";

    commentSection.innerHTML += `
    <div class='comment-form comment-form-${postID} edit-comment-form-${postID}'>
    <div class='user-image-comment'>
              <img src='${profilePic}' class='post-avatar post-avatar-30' />
    </div>
      <form onsubmit ="return saveEditComment(${postID},${commentID},'${user}','${profilePic}','${time}')"  method="post" id='commentFormEdit_${commentID}'>
      <i class='tooltip-container fas fa-times comment-delete' onclick="javascript:showComment('${user}',${commentID},'${postID}','${profilePic}','${time}','${commentValue}',false)"><span class='tooltip tooltip-right'>Cancel</span></i>
          <input name = "comment_edit_${commentID}" type='text' id='input-edit' autocomplete = "off" value = "${commentValue}" >
          
          <input style='display:none;' type="submit" id="${postID}" value="Comment" > 
      </form>
    </div>`;

    document
      .querySelector(`[name='comment_edit_${commentID}']`)
      .scrollIntoView({
        behavior: "smooth",
        block: "center"
      });
  }
}

function showComment(user, commentID, postID, profilePic, time, comment, flag) {
  //Showing post comment form
  document.querySelector(`.comment-form-${postID}`).style.display = "flex";
  document.querySelector(`.edit-comment-form-${postID}`).remove();

  if (flag) var edited = "Edited";
  else var edited = "";

  document.querySelector(`.comment-${commentID}`).innerHTML = `
  
                
    <div class='user-image'>
      <img src='${profilePic}' class='post-avatar post-avatar-30' />
    </div>
  
    <div class='comment-info'>
      <i class='tooltip-container fas fa-times comment-delete' onclick='javascript:deleteComment(${commentID})'><span class='tooltip tooltip-right'>Remove</span></i>
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

function showEditImageButton(div) {
  document.querySelector(`.${div}`).classList.remove("hidden");
}

function hideEditImageButton(div) {
  document.querySelector(`.${div}`).classList.add("hidden");
}

//Search Function
function getUsers(value, flag) {
  // flag values :
  // 0 - Searching in messages
  // 1 - Normal Searching

  // Setting paramters for POST request
  var param = `query=${value}&flag=${flag}`;
  var searchFooter;

  ajaxCalls("POST", "./includes/AjaxHandlers/AJAX3.php?search=1", param).then(
    function (result) {
      if (flag == 0) conflict = "-message";
      else conflict = "";

      if (result == "No") {
        // If no user found against search
        document.querySelector(".search-result" + conflict).style.display =
          "none";
      } else {
        // Displaying search results
        document.querySelector(".search-result" + conflict).style.display =
          "block";
        document.querySelector(".search-result" + conflict).innerHTML = result;

        if (value.length == 0) {
          document.querySelector(".search-result" + conflict).style.display =
            "none";
          searchFooter = "";
        } else {
          searchFooter = `<a class='see-more' href='allSearchResults.php?query=${value}'>See more</a>`;
          document.querySelector(".search-result").innerHTML += searchFooter;
        }
      }
    }
  );
  // Pain in the ass
  return false;
}

function commentsRefresh() {
  //LOCATION MASLA
  if (
    window.location.pathname == "/socioConnect/main.php" ||
    window.location.pathname == "/main.php"
  ) {
    ajaxCalls("GET", "./includes/AjaxHandlers/AJAX.php?comment=1").then(
      function (result) {
        // Displaying search results
        var data = JSON.parse(result);

        timeToShow = "Just Now";

        for (i = 0; i < data.length; i++) {
          var obj = data[i];

          var comment = `
      <div class='comment comment-${obj.commentID}'>
                    
      <div class='user-image'>
          <a href='timeline.php?visitingUserID=${
            obj.commentUserID
          }'><img src='${
            obj.profilePic
          }' class='post-avatar post-avatar-30' /></a>
      </div>
      
      <div class='comment-info'>
      
      <div class='comment-body'>
      <a href='timeline.php?visitingUserID=${
        obj.commentUserID
      }' class='comment-user'>${obj.name} : </a>
      <span class='comment-text'>${obj.comment}</span>
      <span class='comment-time'>${timeToShow}</span>
      </div>
      </div>
  </div>
         `;

          if (document.querySelector(`.comment-area-${obj.postID}`)) {
            document.querySelector(
              `.comment-area-${obj.postID}`
            ).innerHTML += comment;
          }
        }
      }
    );
  }
}

function notificationRefresh() {
  if (
    window.location.pathname != "/socioConnect/index.php" &&
    window.location.pathname != "/index.php"
  ) {
    ajaxCalls("GET", "./includes/AjaxHandlers/AJAX.php?noti=1").then(function (
      result
    ) {
      // Displaying search results

      // console.log(result);

      var data = JSON.parse(result);

      var notification = "";
      var notiLink = "";
      var conflict = "";
      var notiIcon = "";

      for (i = 0; i < data.length; i++) {
        var obj = data[i];

        // console.log('INSIDE NOTI LOOP');

        if (obj.type == "commented") {
          notiIcon = "far fa-comment-dots";
          conflict = "commented on your post";
        } else if (obj.type == "liked") {
          notiIcon = "far fa-thumbs-up";
          conflict = "liked your post";
        } else if (obj.type == "post") {
          notiIcon = "far fa-user";
          conflict = "posted";
        } else if (obj.type == "request") {
          conflict = "sent you a request";
          notiIcon = "fas fa-user-plus";
          notiLink = "requests.php?notiID=$notiID";
        }

        if (obj.type != "request") {
          notiLink = `
       notification.php?postID=${obj.postID}&type=${obj.type}&notiID=${
            obj.notiID
          } `;
        }

        var notification = `<a href=${notiLink} class='notification noSeen'>
          
                <span class='notification-image'>
                <img src='${
                  obj.profilePic
                }' class='post-avatar post-avatar-30' />
                </span>
                <span class='notification-info'>
            <span class='notification-text'>${
              obj.name
            } has ${conflict}</span><i class='noti-icon ${notiIcon}'></i><span class='noti-time'>Now</span></span></a>
`;
        // Dropdown
        document.querySelector(`.notifications-dropdown`).innerHTML =
          notification +
          document.querySelector(`.notifications-dropdown`).innerHTML;

        if (document.querySelector(".notifications")) {
          document.querySelector(`.notifications`).innerHTML =
            notification + document.querySelector(`.notifications`).innerHTML;
        }
      }
      if (data.length) dropdownCountAjax(1, "noti");
    });
  }
}

function likesRefresh() {
  //LOCATION MASLA
  if (
    window.location.pathname == "/socioConnect/main.php" ||
    window.location.pathname == "/main.php"
  ) {
    ajaxCalls("GET", "./includes/AjaxHandlers/AJAX.php?like=1").then(function (
      result
    ) {
      // console.log('Result :  ');
      // console.log(result);

      // Displaying search results
      var data = JSON.parse(result);

      for (i = 0; i < data.length; i++) {
        var obj = data[i];

        if (document.querySelector(`.like-count-${obj.postID}`)) {
          document.querySelector(`.like-count-${obj.postID}`).innerHTML = `
        <i class='like-count-icon far fa-thumbs-up'></i> ${obj.likes}
                   <span class='tooltip tooltip-bottom count'></span>
        `;
        }
      }
    });
  }
}

function likeUsers(postID) {
  ajaxCalls(
    "GET",
    `./includes/AjaxHandlers/AJAX3.php?likeUsers=1&postID=${postID}`
  ).then(function (result) {
    // Displaying search results
    document.querySelector(`.like-count-${postID} .count`).innerHTML = "";
    var data = JSON.parse(result);
    let flag = true;

    for (i = 0; i < data.length; i++) {
      flag = false;
      var obj = data[i];

      document.querySelector(`.like-count-${postID} .count`).innerHTML += `${
        obj.name
      }<br>`;
    }

    // If no person like that post then not to display tooltip
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
  ajaxCalls("GET", "./includes/AjaxHandlers/AJAX2.php?canMessage=1").then(function (result) {
    if (result) {
      let messageBody = document.messageForm.message_body; // Message text
      let partner = document.messageForm.partner; // Parnter ID
      let pic = document.messageForm.pic; // User Pic

      if (messageBody.value.length > 0) {
        // Making AJAX request with partner ID and message text
        let param = `partner=${partner.value}&messageBody=${messageBody.value}`;

        document.querySelector(".chat-messages").innerHTML += `
          <div class="chat-message my-message">
            <img src='${pic.value}' class='post-avatar post-avatar-30' />
            <span class='message'>${messageBody.value}</span>
            <span class='message-time'>Just now</span>
          </div>
          `;

        ajaxCalls("POST", "./includes/AjaxHandlers/AJAX.php?message=1", param).then(
          function (response) {
            // var msgs = document.querySelectorAll(".chat-message");
            if (
              document.getElementById("loading-messages").innerHTML ==
              "No Messages To Show"
            )
              document.getElementById("loading-messages").innerHTML =
              "No More Messages To Show";
          }
        );

        messageBody.value = "";

        // Scrolling to the most recent message
        var last = document.querySelector(".my-message:last-child");
        last.scrollIntoView({
          behavior: "smooth",
          block: "center"
        });
      }
    } else {
      alert("You've reached your limit of number of messages allowed for a single account");
    }
  });
}

function messageRefresh() {
  var url = window.location.href; // Getting URL of page
  var id = url.substring(url.lastIndexOf("=") + 1); // Extracting ID from URL

  if (
    window.location.pathname == "/socioConnect/messages.php" ||
    window.location.pathname == "/messages.php"
  ) {
    // AJAX call for message Refreshing
    ajaxCalls(
      "GET",
      `./includes/AjaxHandlers/AJAX.php?message=1&id=${id}`
    ).then(function (response) {
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

        // Scrolling to the most recent message
        var last = document.querySelector(".their-message:last-child");
        last.scrollIntoView({
          behavior: "smooth",
          block: "center"
        });
      }
    });
  }
}

function refreshRecentConvos() {
  if (
    window.location.pathname != "/socioConnect/index.php" &&
    window.location.pathname != "/index.php"
  ) {
    // console.log("INSDIE");
    ajaxCalls("GET", "./includes/AjaxHandlers/AJAX.php?recentConvo=1").then(
      function (result) {
        var data = JSON.parse(result);



        if (!(data.notEmpty == "Bilal")) {

          for (i = data.length - 1; i >= 0; i--) {

            var obj = data[i];

            var noSeenClass = 'noSeen';

            var currentIDURL = window.location.search;
            var ID = currentIDURL.slice(currentIDURL.search('=') + 1);


            if (obj.fromID == ID) {
              noSeenClass = '';
            }

            if (
              document.querySelector(".recent-chats .recent-user-" + obj.fromID)
            ) {
              document.querySelector(".recent-chats").removeChild(
                document.querySelector(
                  ".recent-chats .recent-user-" + obj.fromID
                )
              );
            }

            if (
              document.querySelector(
                ".recent-chats-dropdown .recent-user-" + obj.fromID
              )
            ) {
              document
                .querySelector(".recent-chats-dropdown")
                .removeChild(
                  document.querySelector(
                    ".recent-chats-dropdown .recent-user-" + obj.fromID
                  )
                );
            }

            var recentMessage = `
        <div class='recent-user-div recent-user-${obj.fromID} ${noSeenClass}'>
          <a href='messages.php?id=${obj.fromID}' class='recent-user'>
            <span class='recent-user-image'>
              <img src='${obj.pic}' class='post-avatar post-avatar-40' />
            </span>
            <span class='recent-message-info'>
              <span class="recent-username">${obj.partner}</span>
              <span class='recent-message-text'>${obj.from} ${obj.msg}</span>
            </span>
            <span class='recent-message-time'>
              <span>${obj.at}</span>
            </span>

            <span>
            <span class='chat-del-button' >
              <i class='tooltip-container fas fa-times chat-delete' onclick='javascript:deleteConvo(${
                obj.fromID
              })'><span class='tooltip tooltip-left'>Delete</span></i>
            </span>
            </span>
          </a>
          
         
        </div>
        `;
            if (document.querySelector(".recent-chats")) {
              document.querySelector(".recent-chats").innerHTML =
                recentMessage +
                document.querySelector(".recent-chats").innerHTML;
            }

            if (document.querySelector(".recent-chats-dropdown")) {
              // console.log(recentMessage);
              document.querySelector(".recent-chats-dropdown").innerHTML =
                recentMessage +
                document.querySelector(".recent-chats-dropdown").innerHTML;
            }
          }

          dropdownCountAjax(2, "msg");
        }
      }
    );
  }
}

function deleteConvo(id) {
  // For deleting User Chat
  // id - loggedIn userID

  var url = window.location.href; // URL of the current window
  //var openConvoId = url.slice(46); // Will give the ID of the user whom you are chatting with

  var openConvoId = url.substring(url.lastIndexOf("=") + 1); // Extracting ID from URL

  let param = `id=${id}&urlID=${openConvoId}`;
  ajaxCalls(
    "POST",
    `./includes/AjaxHandlers/AJAX2.php?deleteConvo=1`,
    param
  ).then(function (response) {
    // console.log(response);
    //If response is not a redirection, this would be changed if this comment is removed from messags.php
    if (response == "Reload the page") {
      window.location.href = "messages.php"; // Redirect the user to message Page
    } else {
      document.querySelector(".recent-chats").innerHTML = response;
    }
  });
}

function showPageMessages(id, page) {
  document.getElementById("loading-messages").style.display = "none";
  ajaxCalls(
      "GET",
      `./includes/AjaxHandlers/AJAX3.php?messagePage=1&id=${id}&page=${page}`
    )
    .then(function (result) {
      document.querySelector(".chat-messages").innerHTML =
        result + "<br>" + document.querySelector(".chat-messages").innerHTML;
      document.getElementById("loading-messages").style.display = "block";

      if (document.getElementById("noMoreMessages").value == "true")
        document.getElementById("loading-messages").innerHTML =
        "No More Messages To Show";
      // document.getElementById("noMoreMessages").scrollIntoView();
      document.getElementsByTagName("br")[0].scrollIntoView();
    })
    .catch(function (reject) {});
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
  }
}

function removeFriend(id) {
  var path = window.location.pathname;
  var args = window.location.search;
  var flag = "";

  if (path != "/socioConnect/requests.php" && path != "/requests.php") {
    flag = " limit 10";
  }

  var redirectionFlag = true;

  //if you are on someone else's friends page, then don't resfresh the page, just change the icon
  if (args) redirectionFlag = false;

  let param = `friendId=${id}&conflict=${flag}`;
  ajaxCalls(
    "POST",
    "./includes/AjaxHandlers/AJAX2.php?removeFriend=1",
    param
  ).then(function (result) {
    if (redirectionFlag) {
      var data = JSON.parse(result);

      //console.log(data);
      document.querySelector(".friends-container").innerHTML = "";
      flag = 0;
      // console.log(data.length);
      for (i = 0; i < data.length; i++) {
        flag++;
        var obj = data[i];
        obj.profile_pic = "./assets/profile_pictures/" + obj.profile_pic;
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
                <span class='${obj.state}'>${obj.time}</span>         
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
        if (
          flag == 10 &&
          path != "/socioConnect/requests.php" &&
          path != "/requests.php"
        )
          break;
      }
      if (path != "/socioConnect/requests.php" && path != "/requests.php") {
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
      }
    } else {
      var personLink = document.querySelector(`.remove-friend-${id}`);
      personLink.className = `add-friend add-friend-${id}`;
      personLink.setAttribute("href", `javascript:addFriend(${id})`);
      personLink.querySelector(".tooltip").innerHTML = "Add Friend";

      fontAwesomeIcon = personLink.querySelector(".tooltip-container");
      fontAwesomeIcon.classList.remove("fa-times");
      fontAwesomeIcon.classList.add("fa-plus");
    }
  });
}

function addFriend(id) {
  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX2.php?canAdd=${id}`).then(function (result) {
    if (result) {
      var personLink = document.querySelectorAll(`.add-friend-${id}`);
      var fontAwesomeIcon;
      for (var i = 0; i < personLink.length; i++) {
        fontAwesomeIcon = personLink[i].querySelector(".tooltip-container");
        fontAwesomeIcon.classList.remove("fa-plus");
        fontAwesomeIcon.classList.add("fa-check");
      }

      let param = `id=${id}`;
      ajaxCalls(
        "POST",
        "./includes/AjaxHandlers/AJAX2.php?addFriend=1",
        param
      ).then(function (result) {
        for (var i = 0; i < personLink.length; i++) {
          personLink[i].setAttribute("href", `javascript:cancelReq(${id})`);
          personLink[i].querySelector(".tooltip").innerHTML = "Friend Request Sent";
        }
      });
    } else {
      alert("You have reached your limit of number of friend requests allowed for a single account");
    }
  });
}

function cancelReq(id) {
  var personLink = document.querySelectorAll(`.add-friend-${id}`);
  var fontAwesomeIcon;
  for (var i = 0; i < personLink.length; i++) {
    fontAwesomeIcon = personLink[i].querySelector(".tooltip-container");
    fontAwesomeIcon.classList.remove("fa-check");
    fontAwesomeIcon.classList.add("fa-plus");
  }
  let param = `id=${id}`;
  ajaxCalls(
    "POST",
    "./includes/AjaxHandlers/AJAX2.php?cancelReq=1",
    param
  ).then(function (result) {
    for (var i = 0; i < personLink.length; i++) {
      personLink[i].setAttribute("href", `javascript:addFriend(${id})`);
      personLink[i].querySelector(".tooltip").innerHTML = "Add Friend";
    }
  });
}

function showPage(flag, page) {
  document.getElementById("loading").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open(
    "GET",
    `./includes/AjaxHandlers/AJAX2.php?loadPosts=1&flag=${flag}&page=${page}`,
    true
  );
  xhr.onload = function () {
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
    setTimeout(function () {
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
  xhr.open(
    "GET",
    "./includes/AjaxHandlers/AJAX3.php?notiPage=1&page=" + page,
    true
  );
  xhr.onload = function () {
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
  var args = window.location.search;
  var id = args.slice(args.search("=") + 1);
  var param;
  if (id == "") param = "loadRA=1&page=" + page;
  else param = "loadRA=1&page=" + page + "&id=" + id;
  document.getElementById("loading-activities").style.display = "none";
  var xhr = new XMLHttpRequest();
  xhr.open("GET", `./includes/AjaxHandlers/AJAX2.php?${param}`, true);
  xhr.onload = function () {
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
window.onclick = function (e) {
  if (
    window.location.pathname != "/socioConnect/index.php" &&
    window.location.pathname != "/index.php"
  ) {
    if (e.srcElement.className != "search-input") {
      document.querySelector(".search-result").style.display = "none";
    }

    if (
      document.querySelector(".search-result-message") &&
      e.srcElement.className != "search-result-message"
    ) {
      document.querySelector(".search-result-message").style.display = "none";
    }

    // Will loop through the array of dropdowns
    let arr = ["noti", "msg", "req"];
    arr.forEach(function (value) {
      if (!e.srcElement.classList.contains(`${value}-click`) &&
        !e.srcElement.classList.contains(`${value}-dropdown`)
      ) {
        // console.log('Here');
        document.querySelector(`.${value}-dropdown`).style.display = "none";
      }
    });
  }
};

/* ------------------------------------------------------------------------------ */

function editCoverPicture() {
  // console.log('here');

  var coverPicData = document.querySelector("input[name='cover-pic']");

  var coverPic = coverPicData.files[0];

  // console.log(coverPic);

  var formData = new FormData();
  formData.append("cover_pic", coverPic);

  ajaxCalls(
    "POST",
    "./includes/AjaxHandlers/AJAX3.php?pic=1",
    formData,
    "pic"
  ).then(function (result) {
    result = "./assets/cover_pictures/" + result;
    document.querySelector(
      ".user-cover"
    ).style.backgroundImage = `url(${result})`;
  });
}

function editProfilePicture() {
  // console.log('Proifle');

  var ProfilePicData = document.querySelector("input[name='profile-pic']");

  var ProfilePic = ProfilePicData.files[0];

  // console.log(ProfilePic);

  var formData = new FormData();
  formData.append("profile_pic", ProfilePic);

  ajaxCalls(
    "POST",
    "./includes/AjaxHandlers/AJAX3.php?pic=1",
    formData,
    "pic"
  ).then(function (result) {
    // console.log(result);
    document.querySelector("#profile_picture").src =
      "./assets/profile_pictures/" + result;
  });
}

//DP Animation Functions
function validateNewPassword(newPass, rePass) {
  var errorMessage = "";
  var error = [];
  var flag1 = (flag2 = false);

  if (newPass != rePass) {
    error.push("s Don't Match");
    flag1 = true;
  } else {
    if (newPass.length < 8) {
      flag1 = true;
      error.push("'s length must be greater than 8 characters");
    }
    if (!(/\d/.test(newPass) && newPass.match(/[a-z]/i))) {
      flag2 = true;
      error.push(" must contain alphanumeric characters");
    }
  }
  if (flag1 && flag2) errorMessage = "Password" + error[0] + " and" + error[1];
  else if (flag1 || flag2) errorMessage = "Password" + error[0];

  if (flag1 || flag2) {
    alert(errorMessage);
    document.querySelector(".user-edit-new-repeat-password").value = "";
    document.querySelector(".user-edit-new-password").value = "";
    return false;
  } else {
    return true;
  }
}

function changePassword() {
  var newPass = document.querySelector("input[name = 'newPassword']");
  var rePass = document.querySelector("input[name = 'rePass']");
  newPass = newPass.value.trim();
  rePass = rePass.value.trim();

  if (validateNewPassword(newPass, rePass)) {
    saveNewPassword(newPass);
  }
}

function saveNewPassword(newPass) {
  var email = document.querySelector("input[name = 'email']").value;
  param = `password=${newPass}&email=${email}`;
  ajaxCalls(
    "POST",
    "./includes/AjaxHandlers/AJAX2.php?saveNewPassword=1",
    param
  ).then(function (result) {
    if (result == "ok") {
      hideForgotPassWindow();
    }
  });
}

function submitForgotPassForm() {
  var email = document.querySelector("input[name = 'email-for-forgot-pass']")
    .value;
  var answer = document.querySelector(".forgot-password-input").value;
  if (answer.trim().length != 0) {
    ajaxCalls(
      "GET",
      `./includes/AjaxHandlers/AJAX2.php?checkAttempts=1&email=${email}`
    ).then(function (checked) {
      if (checked == "yes") {
        ajaxCalls(
          "GET",
          `./includes/AjaxHandlers/AJAX2.php?validateAnswer=1&answer=${answer.toLowerCase()}&email=${email}`
        ).then(function (result) {
          // console.log("result : ");
          // console.log(result);
          if (result == "Yes") {
            var editDiv = document.querySelector(".forgot-password-div");
            editDiv.innerHTML = `<span><h1 class = "forgot-password-div-heading">Set New Password</h1></span>
                                 <span class="forgot-password-div-close" onclick="hideForgotPassWindow()">&times;</span>
                                 <form action = "javascript:void(0)" method = "post" id = "changetPassForm">                          
                                 <label class = "user-info-for-edit"><input type = "password" name = "newPassword" placeholder='Password' class = "change-pass-input" autocomplete="off" maxlength= "255" required autofocus></label><br>
                                 <label class = "user-info-for-edit"><input type = "password" name = "rePass" class = "change-pass-input" placeholder='Confirm Password' autocomplete="off" maxlength= "255" required></label><br>
                                 <input type = "submit" value = "Save New Password" name="submit" class = "password-edit-save" onclick = "changePassword()">`;
          } else document.querySelector(".forgot-password-message").innerHTML = "Wrong Answer!";
        });
      } else {
        alert(
          "You've entered wrong answer 3 times, this service is not availible for the next " +
          (60 - parseInt(checked / 60)) +
          " minutes"
        );
      }
    });
  }
}

function hideForgotPassWindow() {
  // When model is closed
  var editDiv = document.querySelector(".forgot-password-div-container");
  hideDiv(editDiv);
}

function showForgotPassWindow() {
  // Showing pic in the model
  var editDiv = document.querySelector(".forgot-password-div-container");
  showDiv(editDiv);
  var email = document.querySelector("input[name = 'email-for-forgot-pass']")
    .value;
  ajaxCalls(
    "GET",
    `./includes/AjaxHandlers/AJAX2.php?check_answer=1&email=${email}`
  ).then(function (result) {
    if (result.trim() != "")
      document.querySelector(".forgot-password-question").innerHTML = result;
    else
      document.querySelector(".forgot-password-question").innerHTML =
      "No Question Selected";
  });
}

function showDiv(div) {
  div.classList.add("modal-open");
  div.classList.remove("modal-close");
  div.style.display = "block";
}

function hideDiv(div) {
  div.classList.remove("modal-open");
  div.classList.add("modal-close");

  // Timeout in displaying
  setTimeout(() => {
    div.style.display = "none";
  }, 550);
}

function hideEditInfoDiv() {
  // When model is closed
  var editDiv = document.querySelector(".user-info-edit-div-container");
  hideDiv(editDiv);
}

function showEditInfoDiv() {
  //Getting current info

  var editDiv = document.querySelector(".user-info-edit-div-container");
  showDiv(editDiv);
  var skul = document.querySelector(".user-school").innerHTML;
  var colg = document.querySelector(".user-college").innerHTML;
  var uni = document.querySelector(".user-university").innerHTML;
  var work = document.querySelector(".user-work").innerHTML;
  var cntct = document.querySelector(".user-contact").innerHTML;
  var actualAge = document.querySelector(".actualAge").value;
  var gender = document.querySelector(".user-gender").innerHTML;
  var question = document.querySelector(".user-question").innerHTML;

  //Setting Values in input fields
  var defaultVaue = "-";
  if (skul.trim() == defaultVaue) skul = "";
  if (colg.trim() == defaultVaue) colg = "";
  if (uni.trim() == defaultVaue) uni = "";
  if (work.trim() == defaultVaue) work = "";
  if (cntct.trim() == defaultVaue) cntct = "";
  if (question.trim() == defaultVaue) question = "";

  document.querySelector(".user-edit-school").value = skul;
  document.querySelector(".user-edit-college").value = colg;
  document.querySelector(".user-edit-university").value = uni;
  document.querySelector(".user-edit-work").value = work;
  document.querySelector(".user-edit-contact").value = cntct;
  document.querySelector(".user-edit-age").value = actualAge;
  document.querySelector(".user-edit-question").value = question;
  document.querySelector(".user-edit-gender").value = gender.trim();
}

function submitEditInfoForm() {
  var oldPass = document.querySelector(".user-edit-old-password").value;
  var newPass = document.querySelector(".user-edit-new-password").value;
  var rePass = document.querySelector(".user-edit-new-repeat-password").value;
  var school = document.querySelector(".user-edit-school").value;
  var college = document.querySelector(".user-edit-college").value;
  var university = document.querySelector(".user-edit-university").value;
  var work = document.querySelector(".user-edit-work").value;
  var contact = document.querySelector(".user-edit-contact").value;
  var age = document.querySelector(".user-edit-age").value;
  var question = document.querySelector(".user-edit-question").value;
  var answer = document.querySelector(".user-edit-answer").value;
  var genderDropDown = document.querySelector(".user-edit-gender");
  var gender = genderDropDown.options[genderDropDown.selectedIndex].value;
  param = `password=${oldPass}&newPassword=${newPass}&school=${school}&college=${college}&university=${university}&work=${work}&age=${age}&contact=${contact}&genderBox=${gender}&question=${question}&answer=${answer}
  `;



  //Password Validation
  flag = true;
  if (newPass) {
    if (!validateNewPassword(newPass, rePass)) flag = false;
  } else if (oldPass) {
    //Do nothin, just to make an exception from else
  } else {
    alert("Password field can't be empty");
    flag = false;
  }
  if (flag) {
    ajaxCalls("POST", "./includes/EventHandlers/editInfo.php", param).then(
      function (result) {

        if (result > 13) {
          document.querySelector('.edit-error-msg').innerHTML = '';

          infos = {
            school: school,
            college: college,
            university: university,
            work: work,
            contact: contact,
            age: result,
            question: question,
            gender: gender
          };

          for (info in infos) {
            document.querySelector(`.user-${info}`).innerHTML = infos[info];
          }
          hideEditInfoDiv();
          document.querySelector(".user-edit-old-password").value = "";
        } else {
          document.querySelector('.edit-error-msg').innerHTML = 'Not Old Enough!';
        }
      }
    );
  }
}

function dropdownCountAjax(place, dropdown) {

  ajaxCalls(
    "GET",
    `./includes/AjaxHandlers/AJAX3.php?dpCount=${place}&class=${dropdown}`
  ).then(function (result) {


    // console.log("Insiadadsdsda");
    document.querySelector(`.${dropdown}-count`).innerHTML = result;
    locStart = result.lastIndexOf("=") + 1;
    locEnd = result.lastIndexOf(";");

    color = result.substring(locStart, locEnd);

    // console.log(toString(color));
    if (color === "'red'") {
      document.querySelector(`.${dropdown}-count`).style.backgroundColor =
        "red";
    } else {
      document.querySelector(`.${dropdown}-count`).style.backgroundColor =
        "transparent";
    }
  });
}

function deleteUser() {
  var id = document.querySelector(".remove-user-input").value;
  ajaxCalls(
    "POST",
    `./includes/AjaxHandlers/AJAX2.php?deleteAccount=${id}`
  ).then(function (result) {
    alert(result);
  });
}

function getUserDetails() {
  var id = document.querySelector(".search-user-details-input").value;
  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX2.php?searchDetailsByAdmin=1&id=${id}`).then(function (result) {
    if (!result) {
      document.querySelector(".user-activities-summary-content-for-admin").innerHTML = "";
      alert("User not found");
    } else {
      document.querySelector(".user-activities-summary-content-for-admin").innerHTML = result;
    }
  });
}


function updateDropdownMsg() {

  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX3.php?updateDropdown=1`).then(function (result) {

    document.querySelector(".recent-chats-dropdown").innerHTML = result;

  });
}

function updateDropdownMsgCount() {

  ajaxCalls("GET", `./includes/AjaxHandlers/AJAX3.php?updateDropdownCount=1`).then(function (result) {


    document.querySelector(".msg-count").innerHTML = result;


    locStart = result.lastIndexOf("=") + 1;
    locEnd = result.lastIndexOf(";");

    color = result.substring(locStart, locEnd);

    // console.log(toString(color));
    if (color === "'red'") {
      document.querySelector(`.msg-count`).style.backgroundColor =
        "red";
    } else {
      document.querySelector(`.msg-count`).style.backgroundColor =
        "transparent";
    }
  });
}



setInterval(refreshRecentConvos, 1000);
setInterval(commentsRefresh, 3000);
setInterval(notificationRefresh, 3000);
setInterval(likesRefresh, 3000);
setInterval(messageRefresh, 1000);
setInterval(updateDropdownMsg, 1000);
setInterval(updateDropdownMsgCount, 1000);