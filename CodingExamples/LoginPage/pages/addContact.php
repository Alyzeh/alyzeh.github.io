<?php

/* HERE I REQUIRE AND USE THE STICKYFORM CLASS THAT DOES ALL THE VALIDATION AND CREATES THE STICKY FORM.  THE STICKY FORM CLASS USES THE VALIDATION CLASS TO DO THE VALIDATION WORK.*/
require_once('classes/StickyForm.php');
$stickyForm = new StickyForm();
$msg = "";

/*THE INIT FUNCTION IS WRITTEN TO START EVERYTHING OFF IT IS CALLED FROM THE INDEX.PHP PAGE */
function init(){
  global $elementsArr, $stickyForm;
  session_start();
  if($_SESSION['access'] !== "accessGranted"){
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
	"name"=>[
	  "errorMessage"=>"<span class='errorinsert'>Name cannot be blank and must be a standard name.</span>",
    "errorOutput"=>"",
    "type"=>"text",
    "value"=>"Scott",
		"regex"=>"name"
	],
  "address"=>[
    "errorMessage"=>"<span class='errorinsert'>Address cannot be blank and must be a standard address.</span>",
    "errorOutput"=>"",
    "type"=>"text",
    "value"=>"123 Someplace",
    "regex"=>"address"
  ],
  "city"=>[
    "errorMessage"=>"<span class='errorinsert'>City cannot be blank and must only contain letters.</span>",
    "errorOutput"=>"",
    "type"=>"text",
    "value"=>"Any City",
    "regex"=>"name"
  ],
   "state"=>[
    "type"=>"select",
    "options"=>["mi"=>"Michigan","oh"=>"Ohio","il"=>"Illinois","in"=>"Indiana", "wi"=>"Wisconsin"],
    "selected"=>"mi",
    "regex"=>"name"
  ],

	"phone"=>[
		"errorMessage"=>"<span class='errorinsert'>Phone cannot be blank and must be a valid phone number.</span>",
    "errorOutput"=>"",
    "type"=>"text",
		"value"=>"999.999.9999",
		"regex"=>"phone"
  ],
  "email"=>[
    "errorMessage"=>"<span class='errorinsert'>Email cannot be blank and must be a valid email address.</span>",
    "errorOutput"=>"",
    "type"=>"text",
    "value"=>"123@example.com",
    "regex"=>"email"
  ],
  "dob"=>[
    "errorMessage"=>"<span class='errorinsert'>Date of birth cannot be left blank and must be in proper MM/DD/YYYY format.</span>",
    "errorOutput"=>"",
    "type"=>"date",
    "value"=>"2000-01-01",
    "regex"=>"dob"
    
  ],
  

  
 
  "contactmethod"=>[
    "type"=>"checkbox",
    "action"=>"notRequired",
    "status"=>["newsletter"=>"", "emailUpdates"=>"", "textUpdates"=>""]
  ],
  "age"=>[
    "errorMessage"=>"<span class='errorinsert'>You must select an age range.</span>",
    "errorOutput"=>"",
    "action"=>"required",
    "type"=>"radio",
    "value"=>["10-18"=>"", "19-30"=>"", "31-50"=>"", "51+"=>""]
  ]
];


/*THIS FUNCTION CAN BE CALLED TO ADD DATA TO THE DATABASE */
function addData($post){
  global $elementsArr, $msg;  
  /* IF EVERYTHING WORKS ADD THE DATA HERE TO THE DATABASE HERE USING THE $_POST SUPER GLOBAL ARRAY */
      //print_r($_POST);
      require_once('classes/Pdo_methods.php');

      $pdo = new PdoMethods();

      $sql = "INSERT INTO contactMod (name, address, city, state, phone, email, dob, contactmethod, age) VALUES (:name, :address, :city, :state, :phone, :email, :dob, :contactmethod, :age)";

      /* THIS TAKE THE ARRAY OF CHECK BOXES AND PUT THE VALUES INTO A STRING SEPERATED BY COMMAS  */
      if(isset($_POST['contactmethod'])){
        $contactmethod = "";
        foreach($post['contactmethod'] as $v){
          $contactmethod .= $v.",";
        }
        /* REMOVE THE LAST COMMA FROM THE CONTACTS */
        $contactmethod = substr($contactmethod, 0, -1);
      }
      else {
        $contactmethod = "";
      }

      if(isset($_POST['age'])){
        $age = $_POST['age'];
      }
      


      $bindings = [
        [':name',$post['name'],'str'],
        [':address',$post['address'],'str'],
        [':city',$post['city'],'str'],
        [':state',$post['state'],'str'],
        [':phone',$post['phone'],'str'],
        [':email',$post['email'],'str'],
        [':dob',$post['dob'],'str'],
        [':contactmethod',$contactmethod,'str'],
        [':age',$age,'str']
      ];

      $result = $pdo->otherBinded($sql, $bindings);

      if($result == "error"){
        $msg = "<p>There was a problem processing your form</p>";
        return getForm("<p>There was a problem processing your form</p>", $elementsArr);
      }
      else {
        $msg = "<p>Contact Information Added</p>";
        return getForm("<p>Contact Information Added</p>", $elementsArr);
      }
      
}
   

/*THIS IS THEGET FROM FUCTION WHICH WILL BUILD THE FORM BASED UPON UPON THE (UNMODIFIED OF MODIFIED) ELEMENTS ARRAY. */
function getForm($acknowledgement, $elementsArr){

global $stickyForm, $msg;
$options = $stickyForm->createOptions($elementsArr['state']);
$acknowledgement = "<h1>Add Contact</h1><br/>" . $msg;

/* THIS IS A HEREDOC STRING WHICH CREATES THE FORM AND ADD THE APPROPRIATE VALUES AND ERROR MESSAGES */
$form = <<<HTML
    <form method="post" action="index.php?page=addContact">
    <div class="form-group">
      <label for="name">Name (letters only){$elementsArr['name']['errorOutput']}</label>
      <input type="text" class="form-control" id="name" name="name" value="{$elementsArr['name']['value']}" >
    </div>
    <div class="form-group">
      <label for="address">Address (just number and street){$elementsArr['address']['errorOutput']}</label>
      <input type="text" class="form-control" id="address" name="address" value="{$elementsArr['address']['value']}" >
    </div>
    <div class="form-group">
      <label for="city">City{$elementsArr['city']['errorOutput']}</label>
      <input type="text" class="form-control" id="city" name="city" value="{$elementsArr['city']['value']}" >
    </div>
    <div class="form-group">
      <label for="state">State</label>
      <select class="form-control" id="state" name="state">
        $options
      </select>
    </div>
    <div class="form-group">
      <label for="phone">Phone (format 999.999.9999) {$elementsArr['phone']['errorOutput']}</label>
      <input type="text" class="form-control" id="phone" name="phone" value="{$elementsArr['phone']['value']}" >
    </div>
    <div class="form-group">
      <label for="email">Email {$elementsArr['email']['errorOutput']}</label>
      <input type="text" class="form-control" id="email" name="email" value="{$elementsArr['email']['value']}" >
    </div>
    <div class="form-group">
      <label for="dob">Date of Birth {$elementsArr['dob']['errorOutput']}</label>
      <input type="date" class="form-control" id="dob" name="dob" value="{$elementsArr['dob']['value']}" >

    </div>
            
    
    <p>Please check all contact methods you would like (optional):</p>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="contactmethod[]" id="contact1" value="newsletter" {$elementsArr['contactmethod']['status']['newsletter']}>
      <label class="form-check-label" for="contactmethod1">Newsletter</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="contactmethod[]" id="contact2" value="emailUpdates" {$elementsArr['contactmethod']['status']['emailUpdates']}>
      <label class="form-check-label" for="contactmethod2">Email Updates</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" name="contactmethod[]" id="contact3" value="textUpdates" {$elementsArr['contactmethod']['status']['textUpdates']}>
      <label class="form-check-label" for="contactmethod3">Text Updates</label>
    </div>
        
    <p>Please select an age range (required):{$elementsArr['age']['errorOutput']}</p>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="age" id="age1" value="10-18"  {$elementsArr['age']['value']['10-18']}>
      <label class="form-check-label" for="age1">10-18</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="age" id="age2" value="19-30"  {$elementsArr['age']['value']['19-30']}>
      <label class="form-check-label" for="age2">19-30</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="age" id="age3" value="31-50"  {$elementsArr['age']['value']['31-50']}>
      <label class="form-check-label" for="age3">31-50</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="radio" name="age" id="age4" value="51+"  {$elementsArr['age']['value']['51+']}>
      <label class="form-check-label" for="age4">51+</label>
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