<?php 

$location = $_SERVER['REQUEST_URI'];

$loc = substr($location,1,12);

?>
<div class='footer'>
    <div class='footer-logo'>
        <h1>Socio Connect</h1>
    </div>
    <div class='owners'>
        <span>Developed By</span>
        <?php
            // $loc == 'socioConnect' 
            if(true){ ?>
                <span>
                    <a class='owner-names sponsor-link' href="https://www.facebook.com/ahmed.abdullah.378" target="_blank">Ahmed Abdullah</a>, 
                    <a class='owner-names sponsor-link' href="https://www.facebook.com/03433589449b" target="_blank">Bilal Asif</a> &nbsp& 
                    <a class='owner-names sponsor-link' href="https://www.facebook.com/pro123908" target="_blank">Bilal Ahmad</a> 
                </span>
                <span><a class='owner-names sponsor-link contact-info' href= "mailto:pro123908@gmail.com">pro123908@gmail.com</a></span>
                <span><a class='owner-names sponsor-link contact-info' href= "mailto:ahmed067abdullah@gmail.com" >ahmed067abdullah@gmail.com</a></span>
                <span><a class='owner-names sponsor-link contact-info' href= "mailto:bilal069@icloud.com">bilal069@icloud.com</a></span>
            <?php } 
            else { ?> 
                <span>
                    <a class='owner-names sponsor-link' href="https://www.facebook.com/ahmed.abdullah.378" target="_blank">Ahmed Abdullah</a> & 
                    <a class='owner-names sponsor-link' href="https://www.facebook.com/pro123908" target="_blank">Bilal Ahmad</a> 
                </span>
                <span><a class='owner-names sponsor-link contact-info' href= "mailto:pro123908@gmail.com">pro123908@gmail.com</a></span>
                <span><a class='owner-names sponsor-link contact-info' href= "mailto:ahmed067abdullah@gmail.com" >ahmed067abdullah@gmail.com</a>
            <?php }
        ?>
    </div>
    <div class='copyright'>
        <?php if($loc != 'socioConnect') { ?>
        Sponsored by <a href="https://www.facebook.com/abmotorskarachi/?ref=br_rs" target="_blank" class="owner-names sponsor-link">A.B Motors</a><br>
        <?php } ?>
        &copy; 2018 Copyrights. All Rights Reserved.
    </div>
</div>
