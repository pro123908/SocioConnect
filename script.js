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

document.querySelector('#commentForm').addEventListener('submit',(e)=>{
  e.preventDefault();
  console.log('Submitted');

  var comment = document.querySelector('input[name="comment"]').value;
  var post = document.querySelector('input[name="post_id"]').value;
  var param = `comment=${comment}&post_id=${post}`;

  var xhr = new XMLHttpRequest();

  xhr.open('POST','comment.php',true);
  xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");


  xhr.onload = () => {
      console.log(this.responseText);
    
  }
  xhr.send(param);
  
  
});