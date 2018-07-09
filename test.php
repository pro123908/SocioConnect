<?php

    if(isset($_POST['name'])){
        $name = $_POST['name'];
        $age = $_POST['age'];


        
        
        $query = mysql_query("Insert into users(name,age) values('$name',$age)");

        $query = mysql_query("select * from users");
        while($row = mysqli_fetch_assoc($query)){
            $name = $row['name'];
            echo $name;

            
        }

    }

?>


<form action="test.php" method='POST'>
    <input type="text" name='name'>
    <input type="submit">
    
</form>