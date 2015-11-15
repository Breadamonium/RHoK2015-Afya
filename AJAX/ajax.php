<?php
require ('../volunteermanagement.php');
if(!empty($_POST['name'])) {
	try {
		$db = new volunteermanagementsystem('../volunteerdatabase.db');
	} catch (Exception $e){
		echo "Error";
		return;
	}
	$name = $_POST['name'];
	switch($name) {
		case 'username':
			echo $db->{'username_exists'}($_POST['value']);
			return;
		case 'email':
			echo $db->{'username_exists'}($_POST['value']);
			return;
		case 'phonenumber':
			echo "nope";
			return;
		case 'register-form':
			$username = $_POST['username'];
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$email = $_POST['email'];
			$phonenumber = intval($_POST['phonenumber']);
			$isGroup = $_POST['isGroup'];
			$isUnder18 = $_POST['isUnder18'];
			if($isGroup == "true") {
				$isGroup = true;
			} else {
				$isGroup = false;
			}
			if($isUnder18 == "true")
				$isUnder18 = true;
			else {
				$isUnder18 = false;
			}
			if($isGroup) {
				echo $db->{'new_group'}($username);
			} else {
				echo $db->{'new_volunteer'}($firstname, $lastname, $username, NULL, $phonenumber, $email, NULL, $isUnder18);
			}
			return;
	}
}

?>