<?php

	session_start();
	session_unset();
	session_destroy();
	header('Location: http://russet.wccnet.edu/~brouthier/assignments/assignment10/index.php?page=login');


?>