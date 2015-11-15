<?php
require ('../volunteermanagement.php');
if(!empty($_POST['name'])) {
	$name = $_POST['name'];
	switch($name) {
		case 'username':
			try {
				$db = new volunteermanagementsystem('../volunteerdatabase.db');

			} catch (Exception $e){
				echo "Error";
			}
			echo $db->{'username_exists'}($_POST['value']);
			return;
		case 'email':
			try {
				$db = new volunteermanagementsystem('../volunteerdatabase.db');

			} catch (Exception $e){
				echo "Error";
			}
			echo $db->{'username_exists'}($_POST['value']);
			return;
		case 'phonenumber':
			return;
		case 'register-form':
			return;
	}
}

?>