function showCommentField(id){
  document.getElementById("post_id_"+id).classList.toggle('hidden');
}

function like(postID){

  var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
           if (xmlhttp.status == 200) {
            document.querySelector(`.likeCount-${postID}`).textContent = this.responseText.trim();
           }
           else if (xmlhttp.status == 400) {
              alert('There was an error 400');
           }
           else {
               alert('something else other than 200 was returned');
           }
        }
    };

    xmlhttp.open("GET", `like.php?like=${postID}`, true);
    xmlhttp.send();

  
}


function comment(postID){

  var xmlhttp = new XMLHttpRequest();

    var post = document.querySelector(`input[name='post_id_${postID}']`);
    var comment = document.querySelector(`input[name='comment_${post.value}']`);
    var user = document.querySelector('input[name="post_user"]');
    
     
    var param = `comment=${comment.value}&post_id=${post.value}`;

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
           if (xmlhttp.status == 200) {
            commentID = this.responseText.trim();
            document.querySelector(`.commentArea_${post.value}`).innerHTML += `
            <div class='comment comment_${commentID}'>
            <a class='commentDelete' href="javascript:deleteComment(${commentID})">X</a>
              <span class='commentUser'>${user.value} : </span>
              <span class='commentText'>${comment.value}</span>
              <span class='commentTime'>1 Second Ago</span>
            </div>
       `
       comment.value = '';
           }
           else if (xmlhttp.status == 400) {
              alert('There was an error 400');
           }
           else {
               alert('something else other than 200 was returned');
           }
        }
    };

    xmlhttp.open('POST','comment.php',true);
    xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlhttp.send(param);
    
    
  return false;


  
}


function deletePost(postID){
  console.log('Done');

  var xmlhttp = new XMLHttpRequest();

    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
           if (xmlhttp.status == 200) {
            console.log('Response : ' + this.responseText);
            document.querySelector(`.post_${postID}`).style.display = 'none';
           }
           else if (xmlhttp.status == 400) {
              alert('There was an error 400');
           }
           else {
               alert('something else other than 200 was returned');
           }
        }
    };

    xmlhttp.open("GET", `delete.php?id=${postID}`, true);
    xmlhttp.send();

}

function addPost(user_id){
    var post = document.querySelector("textarea[name='post']");
    var param = `post=${post.value}&user_id=${user_id}`;
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
           if (xhr.status == 200) {      
                document.querySelector("#postArea").innerHTML = this.responseText + document.querySelector("#postArea").innerHTML;
        }
           else if (xhr.status == 400) {
              alert('There was an error 400');
           }
           else {
               alert('something else other than 200 was returned');
           }
        }
    };
        

    xhr.open("POST","post.php",true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");    
    xhr.send(param);

}


function deleteComment(commentID){
console.log(commentID);


var xmlhttp = new XMLHttpRequest();

xmlhttp.onreadystatechange = function() {
    if (xmlhttp.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
       if (xmlhttp.status == 200) {
        console.log('Response : ' + this.responseText);
        document.querySelector(`.comment_${commentID}`).style.display = 'none';
        
       }
       else if (xmlhttp.status == 400) {
          alert('There was an error 400');
       }
       else {
           alert('something else other than 200 was returned');
       }
    }
};

xmlhttp.open("GET", `commentDelete.php?id=${commentID}`, true);
xmlhttp.send();
}

//DP Animation Functions
function onClosedImagModal(){
    var modal = document.getElementById("modal");
    modal.classList.remove('modal-open');
    modal.classList.add('modal-close');
    setTimeout(()=>{ modal.style.display = "none"; }, 550);
}
function showImage(){
    
    var modal = document.getElementById("modal");
    modal.classList.add('modal-open');         
    modal.classList.remove('modal-close');
    modal.style.display = "block";
}

//Search Function

function getUsers(value,user_id){
    var xhr = new XMLHttpRequest();
    
    var param = `query=${value}&user_id=${user_id}`;
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState == XMLHttpRequest.DONE) {   // XMLHttpRequest.DONE == 4
            if (xhr.status == 200) { 
                        
            document.querySelector(".search_results").innerHTML = this.responseText;
        }
           else if (xhr.status == 400) {

              alert('There was an error 400');
           }
           else {
               alert('something else other than 200 was returned');
           }
        }
    };

    xhr.open('POST',"search.php",true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");  
    xhr.send(param);
     
  return false;

    
}

