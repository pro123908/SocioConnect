function showCommentField(id){
  alert('in show');
  document.getElementById("post_id_"+id).classList.toggle('hidden');
}

function like(postID){
  var xhr = new XMLHttpRequest();

  xhr.open('GET',`like.php?like=${postID}`,true);

  xhr.onload = function(){
    if(this.status == 200){
      console.log(this.responseText);
    }
  }

  xhr.send();
  
}