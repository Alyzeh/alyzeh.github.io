<?php
/* THIS ENTIRE PAGE IS JUST A PLACEHOLDER PAGE WHICH THE FORM WILL BE INJECTED INTO */
/*I REQUIRE IN THE ROUTES PAGE WHICH IS ACTUALLY DOES THE WORK FOR GETTING THE PAGES.*/ 
require_once('pages/routes.php');


?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<title><?php echo $title; ?> || Add/Delete Contacts</title>
		<meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
		<link rel="stylesheet" href="css/style.css">
		
	</head>

	<body class="container">
		<?php
			/* THIS IS THE PHP PAGE  */

			if($title !== "Login"){
				


				if($_SESSION['status'] === "Staff"){
					echo $nav;
				}
				else if($_SESSION['status'] === "Admin"){
					echo $navAdmin;
				}
				
			}



			
			
			/* THE ACKNOWLEDGEMENT GOES HERE AS THE F
			IRST INDEX OF THE ARRAY  */
			echo $result[0]; 

			/* THE FORM GOES HERE.  LOOK AT THE FORM.PHP PAGE TO SEE HOW THE RETURN IN DONE. */
			echo $result[1]; 
		?>
	</body>
</html> 