function showCommentField(id){
  document.getElementById("post_id_"+id).classList.toggle('hidden');
}

function like(postID){
  var xhr = new XMLHttpRequest();

  xhr.open('GET',`like.php?like=${postID}`,true);

  xhr.onload = () => {
    if(this.status == 200){
      document.querySelector(`.likeCount-${postID}`).textContent = this.responseText;
    }
  }

  xhr.send();
  
}

function comment(postID){
  
}

document.querySelectorAll('#commentForm')[1].addEventListener('submit',(e)=>{
  e.preventDefault();
  console.log('Submitted');

  var comment = document.querySelector('input[name="comment"]');
  var post = document.querySelector('input[name="post_id"]');
  var user = document.querySelector('input[name="post_user"]');
  console.log(post.value);
  console.log(comment.value);
  console.log(user.value);
  var param = `comment=${comment.value}&post_id=${post.value}`;

  var xhr = new XMLHttpRequest();

  xhr.open('POST','comment.php',true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");


  xhr.onload = () => {
      console.log(document.querySelector(`.commentArea_${post.value}`));
      document.querySelector(`.commentArea_${post.value}`).innerHTML += `
      <div class='comment'>
        <span class='commentUser'>${user.value} : </span>
        <span class='commentText'>${comment.value}</span>
      </div>
      `

      comment.value = '';
      
      console.log(this.responseText);
    
  }
  xhr.send(param);
  
  
});