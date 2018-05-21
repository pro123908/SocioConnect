<?php
    include "functions.php";
    
    if(strlen($_POST['query']) == 0){
        echo " ";
    }
    else{
        //explode breakes the string into array, each substring is made when the first arg of explode is found in the string
        $names = explode(" ",$_POST['query']);
        if(count($names) ==2)
            //if there there are two substrings then it would search for first substirng in first name and second string in the last name
            $users = queryFunc("SELECT first_name, last_name,profile_pic,username,user_id from users where first_name like '$names[0]%' AND last_name like '$names[1]%'  limit 5" );  
        else
            //if there is only one substring, i.e no spaces are present in the input then it would search that substring in both first name and last name
            $users = queryFunc("SELECT first_name, last_name,profile_pic,username,user_id from users where first_name like '$names[0]%' OR last_name like '$names[0]%' limit 5" );
    
        isData($users);     
        while ($row = isRecord($users)) {
            $user = <<<DELIMETER
            <div class="resultDisplay">
                <a class="search_link" href="timeline.php?visiting={$row['user_id']}">{$row['first_name']} {$row['last_name']} </a>                    
            </div>
DELIMETER;
    echo $user;               
    }
} 
  
?>