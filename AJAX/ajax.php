<?php
require ('../volunteermanagement.php');
if(!empty($_POST['name'])) {
	$name = $_POST['name'];
	if($name == 'username') {
		try {
			$db = new volunteermanagementsystem();
			
		} catch (Exception $e){
			echo "Cannot connect to database.";
		}
		echo true;
	}
	if($name == 'register-form') {
		echo true;
	}
}

?>