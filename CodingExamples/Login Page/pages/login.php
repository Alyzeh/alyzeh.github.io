<?php
  require_once('classes/StickyForm.php');
  $stickyForm = new StickyForm();
  $msg = "";
  function pageload(){
  global $elementsArr, $stickyForm;

  /* IF THE FORM WAS SUBMITTED DO THE FOLLOWING  */
  if(isset($_POST['login'])){

    /*THIS METHODS TAKE THE POST ARRAY AND THE ELEMENTS ARRAY (SEE BELOW) AND PASSES THEM TO THE VALIDATION FORM METHOD OF THE STICKY FORM CLASS.  IT UPDATES THE ELEMENTS ARRAY AND RETURNS IT, THIS IS STORED IN THE $postArr VARIABLE */
    $postArr = $stickyForm->validateForm($_POST, $elementsArr);

    /* THE ELEMENTS ARRAY HAS A MASTER STATUS AREA. IF THERE ARE ANY ERRORS FOUND THE STATUS IS CHANGED TO "ERRORS" FROM THE DEFAULT OF "NOERRORS".  DEPENDING ON WHAT IS RETURNED DEPENDS ON WHAT HAPPENS NEXT.  IN THIS CASE THE RETURN MESSAGE HAS "NO ERRORS" SO WE HAVE NO PROBLEMS WITH OUR VALIDATION AND WE CAN SUBMIT THE FORM */
    if($postArr['masterStatus']['status'] == "noerrors"){
      
      /*addData() IS THE METHOD TO CALL TO ADD THE FORM INFORMATION TO THE DATABASE (NOT WRITTEN IN THIS EXAMPLE) THEN WE CALL THE GETFORM METHOD WHICH RETURNS AND ACKNOWLEDGEMENT AND THE ORGINAL ARRAY (NOT MODIFIED). THE ACKNOWLEDGEMENT IS THE FIRST PARAMETER THE ELEMENTS ARRAY IS THE ELEMENTS ARRAY WE CREATE (AGAIN SEE BELOW) */
      return loginData($_POST);

    }
    else{
      /* IF THERE WAS A PROBLEM WITH THE FORM VALIDATION THEN THE MODIFIED ARRAY ($postArr) WILL BE SENT AS THE SECOND PARAMETER.  THIS MODIFIED ARRAY IS THE SAME AS THE ELEMENTS ARRAY BUT ERROR MESSAGES AND VALUES HAVE BEEN ADDED TO DISPLAY ERRORS AND MAKE IT STICKY */
      return getForm("",$postArr);
    }
    
  }

  /* THIS CREATES THE FORM BASED ON THE ORIGINAL ARRAY THIS IS CALLED WHEN THE PAGE FIRST LOADS BEFORE A FORM HAS BEEN SUBMITTED */
  else {
      return getForm("", $elementsArr);
    } 
}

$elementsArr = [
  "masterStatus"=>[
    "status"=>"noerrors",
    "type"=>"masterStatus"
  ],
  "ad_email"=>[
    "errorMessage"=>"<span class='errorinsert'>Email cannot be blank and must be a valid email address.</span>",
    "errorOutput"=>"",
    "type"=>"text",
    "value"=>"email@example.com",
    "regex"=>"email"
  ],
  "password"=>[
    "errorMessage"=>"<span class='errorinsert'>Password cannot be blank, cannot contain spaces, and must be at least 6 characters in length.</span>",
    "errorOutput"=>"",
    "type"=>"password",
    "value"=>"password",
    "regex"=>"password"
  ]
];

/*THIS FUNCTION CAN BE CALLED TO ADD DATA TO THE DATABASE */
function loginData($post){
  global $elementsArr, $msg;  
  /* IF EVERYTHING WORKS ADD THE DATA HERE TO THE DATABASE HERE USING THE $_POST SUPER GLOBAL ARRAY */
      //print_r($_POST);
      require_once('classes/Pdo_methods.php');

      $pdo = new PdoMethods();

        $sql = "SELECT * FROM admin WHERE ad_email=:ad_email AND password=:password";
  

  /* THESE BINDINGS ARE LATER INJECTED INTO THE SQL STATEMENT THIS PREVENTS SQL INJECTIONS */
    $bindings = [
      [':ad_email',$post['ad_email'],'str'],
      [':password',$post['password'],'str'],
    ];

  $records = $pdo->selectBinded($sql, $bindings);




      $password = $_POST['password'];
      $email = $_POST['ad_email'];

      

      if($records == "error"){
        $msg = "<p>There was a problem processing your form</p>";
        return getForm("<p>There was a problem processing your form</p>", $elementsArr);
        
      }
      else{
            if(count($records) != 0){
                /** IF THE PASSWORD IS NOT VERIFIED USING PASSWORD_VERIFY THEN RETURN FAILED, OTHERWISE RETURN SUCCESS, IF NO RECORDS ARE FOUND RETURN NO RECORDS FOUND. */
                if($records[0]['ad_email'] === $email && $records[0]['password'] === $password){
                  session_start();
                      
                        $_SESSION['ad_name'] = $records[0]['ad_name'];
                        $_SESSION['status'] = $records[0]['status'];
                        $_SESSION['access'] = "accessGranted";
                        header('Location: http://russet.wccnet.edu/~brouthier/assignments/assignment10/index.php?page=welcome');

                }
                else {
                      $msg = "<p>There was a problem logging in with those credentials</p>";
                      return getForm("<p>There was a problem logging in with those credentials</p>", $elementsArr);
                      
                }
                }
                
            else {
                $msg = "<p>There was a problem logging in with those credentials</p>";
                return getForm("<p>There was a problem logging in with those credentials</p>", $elementsArr);
                
            }
        }
      
}
function getForm($acknowledgement, $elementsArr){

global $stickyForm, $msg;


$acknowledgement = "<h1>Login</h1><br /><p>Admin email: email@example.com <br />Staff email: 123@example.com <br /> password for both is password</p>" . $msg; 
 

$form = <<<HTML
        <form method="post" action="index.php?page=login">
          <div class="form-group">
            <label for="ad_email">Email {$elementsArr['ad_email']['errorOutput']}</label>
            <input type="text" class="form-control" id="ad_email" name="ad_email" value="{$elementsArr['ad_email']['value']}" >
          </div>
          <div class="form-group">
            <label for="password">Password {$elementsArr['password']['errorOutput']}</label>
            <input type="password" class="form-control" id="password" name="password" value="{$elementsArr['password']['value']}" >
          </div>
          <div>
          <input type="submit" class="btn btn-primary" name="login" value="Login">
          </div>
        </form>
HTML;

return [$acknowledgement, $form];

}



?>