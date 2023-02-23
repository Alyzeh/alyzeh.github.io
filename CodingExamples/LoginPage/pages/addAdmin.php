<?php

/* HERE I REQUIRE AND USE THE STICKYFORM CLASS THAT DOES ALL THE VALIDATION AND CREATES THE STICKY FORM.  THE STICKY FORM CLASS USES THE VALIDATION CLASS TO DO THE VALIDATION WORK.*/
require_once('classes/StickyForm.php');
$stickyForm = new StickyForm();
$msg = "";
/*THE INIT FUNCTION IS WRITTEN TO START EVERYTHING OFF IT IS CALLED FROM THE INDEX.PHP PAGE */
function init(){
  global $elementsArr, $stickyForm;
  session_start();
  if($_SESSION['status'] !== "Admin"){
    header('Location: http://russet.wccnet.edu/~brouthier/assignments/assignment10/logout.php');
  }

  /* IF THE FORM WAS SUBMITTED DO THE FOLLOWING  */
  if(isset($_POST['submit'])){

    /*THIS METHODS TAKE THE POST ARRAY AND THE ELEMENTS ARRAY (SEE BELOW) AND PASSES THEM TO THE VALIDATION FORM METHOD OF THE STICKY FORM CLASS.  IT UPDATES THE ELEMENTS ARRAY AND RETURNS IT, THIS IS STORED IN THE $postArr VARIABLE */
    $postArr = $stickyForm->validateForm($_POST, $elementsArr);

    /* THE ELEMENTS ARRAY HAS A MASTER STATUS AREA. IF THERE ARE ANY ERRORS FOUND THE STATUS IS CHANGED TO "ERRORS" FROM THE DEFAULT OF "NOERRORS".  DEPENDING ON WHAT IS RETURNED DEPENDS ON WHAT HAPPENS NEXT.  IN THIS CASE THE RETURN MESSAGE HAS "NO ERRORS" SO WE HAVE NO PROBLEMS WITH OUR VALIDATION AND WE CAN SUBMIT THE FORM */
    if($postArr['masterStatus']['status'] == "noerrors"){
      
      /*addData() IS THE METHOD TO CALL TO ADD THE FORM INFORMATION TO THE DATABASE (NOT WRITTEN IN THIS EXAMPLE) THEN WE CALL THE GETFORM METHOD WHICH RETURNS AND ACKNOWLEDGEMENT AND THE ORGINAL ARRAY (NOT MODIFIED). THE ACKNOWLEDGEMENT IS THE FIRST PARAMETER THE ELEMENTS ARRAY IS THE ELEMENTS ARRAY WE CREATE (AGAIN SEE BELOW) */
      return addData($_POST);

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

/* THIS IS THE DATA OF THE FORM.  IT IS A MULTI-DIMENTIONAL ASSOCIATIVE ARRAY THAT IS USED TO CONTAIN FORM DATA AND ERROR MESSAGES.   EACH SUB ARRAY IS NAMED BASED UPON WHAT FORM FIELD IT IS ATTACHED TO. FOR EXAMPLE, "NAME" GOES TO THE TEXT FIELDS WITH THE NAME ATTRIBUTE THAT HAS THE VALUE OF "NAME". NOTICE THE TYPE IS "TEXT" FOR TEXT FIELD.  DEPENDING ON WHAT HAPPENS THIS ASSOCIATE ARRAY IS UPDATED.*/
$elementsArr = [
  "masterStatus"=>[
    "status"=>"noerrors",
    "type"=>"masterStatus"
  ],
	"ad_name"=>[
	  "errorMessage"=>"<span class='errorinsert'>Name cannot be blank and must be a standard name.</span>",
    "errorOutput"=>"",
    "type"=>"text",
    "value"=>"Scott",
		"regex"=>"name"
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
  ],
    "status"=>[
    "type"=>"select",
    "options"=>["Staff"=>"Staff","Admin"=>"Admin"],
    "selected"=>"Staff",
    "regex"=>"name"
  ]
];


/*THIS FUNCTION CAN BE CALLED TO ADD DATA TO THE DATABASE */
function addData($post){
  global $elementsArr, $msg;  
  /* IF EVERYTHING WORKS ADD THE DATA HERE TO THE DATABASE HERE USING THE $_POST SUPER GLOBAL ARRAY */
      //print_r($_POST);
      require_once('classes/Pdo_methods.php');

      $pdo = new PdoMethods();
      $sql = "SELECT ad_email FROM admin WHERE ad_email = :ad_email";
      $bindings = [
        [':ad_email',$post['ad_email'],'str']
      ];

      $records = $pdo->selectBinded($sql, $bindings);

    /** IF THERE WAS AN RETURN ERROR STRING */
      if($records == 'error'){
        $msg = "<p>There was a problem processing your form</p>";
        return getForm("<p>There was a problem processing your form</p>", $elementsArr);
      }
      else{
        if(count($records) != 0){
          $msg = "<p>There is already someone with that username</p>";
          return getForm("<p>There is already someone with that username</p>", $elementsArr);
        }
        else {
          $sql = "INSERT INTO admin (ad_name, ad_email, password, status) VALUES (:ad_name, :ad_email, :password, :status)";


          $bindings = [
            [':ad_name',$post['ad_name'],'str'],
            [':ad_email',$post['ad_email'],'str'],
            [':password',$post['password'],'str'],
            [':status',$post['status'],'str']
          ];

          $result = $pdo->otherBinded($sql, $bindings);
          

          if($result == "error"){
            $msg = "<p>There was a problem processing your form</p>";
            return getForm("<p>There was a problem processing your form</p>", $elementsArr);
          }
          else {
            if($_POST['status'] === 'Admin'){
              $msg = "<p>Administrator Added</p>";
              return getForm("<p>Administrator Added</p>", $elementsArr);
            }
            else {
              $msg = "<p>Staff Member Added</p>";
              return getForm("<p>Staff Member Added</p>", $elementsArr);
              
            }

            
          }
        }

}
}
      
      

   

/*THIS IS THEGET FROM FUCTION WHICH WILL BUILD THE FORM BASED UPON UPON THE (UNMODIFIED OF MODIFIED) ELEMENTS ARRAY. */
function getForm($acknowledgement, $elementsArr){

global $stickyForm, $msg;
$options = $stickyForm->createOptions($elementsArr['status']);
$acknowledgement = "<h1>Add Administrator</h1><br/>" . $msg;
/* THIS IS A HEREDOC STRING WHICH CREATES THE FORM AND ADD THE APPROPRIATE VALUES AND ERROR MESSAGES */
$form = <<<HTML
    <form method="post" action="index.php?page=addAdmin">
    <div class="form-group">
      <label for="ad_name">Name (letters only){$elementsArr['ad_name']['errorOutput']}</label>
      <input type="text" class="form-control" id="ad_name" name="ad_name" value="{$elementsArr['ad_name']['value']}" >
    </div>
    <div class="form-group">
      <label for="ad_email">Email {$elementsArr['ad_email']['errorOutput']}</label>
      <input type="text" class="form-control" id="ad_email" name="ad_email" value="{$elementsArr['ad_email']['value']}" >
    </div>
    <div class="form-group">
      <label for="password">Password {$elementsArr['password']['errorOutput']}</label>
      <input type="password" class="form-control" id="password" name="password" value="{$elementsArr['password']['value']}" >
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select class="form-control" id="status" name="status">
        $options
      </select>
    </div>
    <div>
    <button type="submit" name="submit" class="btn btn-primary">Submit</button>
    </div>
  </form>
HTML;

/* HERE I RETURN AN ARRAY THAT CONTAINS AN ACKNOWLEDGEMENT AND THE FORM.  THIS IS DISPLAYED ON THE INDEX PAGE. */
return [$acknowledgement, $form];

}



?>