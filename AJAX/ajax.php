<?php
require ('../volunteermanagementsystem.php');
if(!empty($_POST['name'])) {
	$name = $_POST['name'];
	if($name == 'username') {
		try {
			$db = new volunteermanagementsystem();
		} catch {
			echo "Cannot connect to database.";
		}
		echo true;
	}
	if($name == 'register-form') {
		echo true;
	}
}

?>