<?php
require_once('classes/StickyForm.php');

$path = "http://russet.wccnet.edu/~brouthier/assignments/assignment10/index.php?page=login";


$nav=<<<HTML
    <nav class="navbar">
        <div class="navbar-nav">
            <a class="nav-item" href="index.php?page=welcome">Welcome</a></div>
            <a class="nav-item" href="index.php?page=addContact">Add Contact Information</a>
            <a class="nav-item" href="index.php?page=deleteContacts">Delete Contact(s)</a>
            <a class="nav-item" href="logout.php">Logout</a>
        </div>
    </nav>
    <br />
HTML;

$navAdmin=<<<HTML
    <nav class="navbar">
        <div class="navbar-nav">
            <a class="nav-item" href="index.php?page=welcome">Welcome</a></div>
            <a class="nav-item" href="index.php?page=addContact">Add Contact Information</a>
            <a class="nav-item" href="index.php?page=deleteContacts">Delete Contact(s)</a>
            <a class="nav-item" href="index.php?page=addAdmin">Add Admin</a></div>
            <a class="nav-item" href="index.php?page=deleteAdmin">Delete Admin(s)</a>
            <a class="nav-item" href="logout.php">Logout</a>
        </div>
    </nav>
    <br />
HTML;


if(isset($_GET)){

    if($_GET['page'] === "addContact"){
       
            require_once('pages/addContact.php');
            $title = "Add Contact";
            $result = init();
        
     

        
    }
    
    else if($_GET['page'] === "deleteContacts"){
   
            require_once('pages/deleteContacts.php');
            $title = "Delete Contact(s)";
            $result = init();
      
        
    }

    else if($_GET['page'] === "welcome"){
   
            require_once('pages/welcome.php');
            $title = "Welcome";
            $result = init();
       

    }

    else if($_GET['page'] === "addAdmin"){
        
            require_once('pages/addAdmin.php');
            $title = "Add Admin";
            $result = init();
       
        
    }

    else if($_GET['page'] === "deleteAdmin"){
  
            require_once('pages/deleteAdmin.php');
            $title = "Delete Admin(s)";
            $result = init();
     
        
    }

    else if($_GET['page'] === "logout"){
        require_once('logout.php');
        $title = "Logout";
        $result = init();
    }

    else if($_GET['page'] === "login"){
        require_once('pages/login.php');
        $title = "Login";
        $result = pageload();
    }


    else {
        header('location: '.$path);
        require_once('pages/login.php');
        
    }
}
   
   







?>