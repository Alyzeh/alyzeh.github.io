<?php

function init(){
        session_start();
        if($_SESSION['access'] !== "accessGranted"){
            header('Location: http://russet.wccnet.edu/~brouthier/assignments/assignment10/logout.php');
        }

    
        $ad_name = $_SESSION['ad_name'];
        $welcome = "<p>Welcome ";
        $welcome .= $ad_name;
        $welcome .= ".</p>";

        return ["<h1>Welcome</h1>",$welcome];
    

    
}

?>