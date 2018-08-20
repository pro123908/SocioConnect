<?php 
	  if(isset($_POST['submit'])){ 
	  if(isset($_GET['go'])){ 
	  if(preg_match("/^[  a-zA-Z]+/", $_POST['title'])){ 
	  $title=$_POST['title']; 
	  //connect  to the database 
	  $db=mysql_connect  ("servername", "username",  "password") or die ('I cannot connect to the database  because: ' . mysql_error()); 
	  //-select  the database to use 
	  $mydb=mysql_select_db("boonge"); 
	  //-query  the database table 
	  $sql="SELECT  pic FROM book WHERE author LIKE '%" . $title .  "%' OR author LIKE '%" . $title ."%'"; 
	  //-run  the query against the mysql query function 
      $result=mysql_query($sql); 
	  //-create  while loop and loop through result set 
	  while($row=mysql_fetch_array($result)){ 
	          $pic  =$row['pic']; 
	         
	  //-display the result of the array 
	  echo "<ul>\n"; 
	  echo "<li>" . "<a  href=\"viewbook.php?id=$ID\">"   .$title . " " . $pic .  "</a></li>\n"; 
	  echo "</ul>"; 
	  } 
	  } 
	  else{ 
	  echo  "<p>Please enter a search query</p>"; 
	  } 
	  } 
	  } 
	?> 

<html> 
	  <head> 
	    <meta  http-equiv="Content-Type" content="text/html;  charset=iso-8859-1"> 
	    <title>Search  books</title> 
	  </head> 
	  <p><body> 
	    <h3>Search books Details</h3> 
	    <p>You  may search either by title or author</p> 
	    <form  method="post" action="search.php?go"  id="searchform"> 
	      <input  type="text" name="name"> 
      <input  type="submit" name="submit" value="Search"> 
	    </form> </p>
	  </body> 
	</html> 

  <td>
  <a href="remove.php?<?php echo $wishID ?>">Close</a>
  </td>
  
	 