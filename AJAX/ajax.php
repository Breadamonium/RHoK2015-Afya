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
		case 'username_registration':
			if($_POST['isGroup'] == 'true') {
				echo $db->{'groupname_exists'}($_POST['value']);
				return;
			}
			echo $db->{'username_exists'}($_POST['value']);
			return;
		case 'username_login':
			if($_POST['isGroup'] == 'true') {
				if($db->{'groupname_exists'}($_POST['value'])) {
					$signed_in = $db->{'group_signedin'}($_POST['value']);
					if($signed_in) {
						echo json_encode(array("signedIn" => "true", "exists" => 1));
						return;
					}
					echo json_encode(array("signedIn" => "false", "exists" => 1));
					//echo "{'signedIn': $signed_in, 'exists':1}";
					return;
				}
				echo json_encode(array("exists" => 0));
				//echo "{'exists':0}";
				return;
			}
			if($db->{'username_exists'}($_POST['value'])) {
				$signed_in = $db->{'user_signedin'}($_POST['value']);
				if($signed_in) {
					echo json_encode(array("signedIn" => "true", "exists" => 1));
					return;
				}
				echo json_encode(array("signedIn" => "false", "exists" => 1));
				//echo "{'signedIn': $signed_in, 'exists':1}";
				return;
			}
			echo json_encode(array("exists" => 0));
			//echo "{'exists':0}";
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
			$findout = $_POST['findout'];
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
				echo $db->{'new_group'}($username, "$firstname $lastname", $phonenumber, $findout);
			} else {
				echo $db->{'new_volunteer'}($firstname, $lastname, $username, NULL, $phonenumber, $email, '', '', $findout, $isUnder18);
			}
			return;
		case 'login-form':
			$username = $_POST['username'];
			$isGroup = $_POST['isGroup'];
			$time = $_POST['time'];
			$groupsize = intval($_POST['size']);
			if($isGroup == "true") {
				$isGroup = true;
			} else {
				$isGroup = false;
			}
			if($isGroup) {
				if($db->{'group_signedin'}($username)) {
					$db->{'signout_group'}($username, $time, $groupsize);
					echo "Your group signed out!";
					return;
				}
				$db->{'signin_group'}($username, $time, '');
				echo "Your group signed in!";
			} else {
				if($db->{'user_signedin'}($username)) {
					$db->{'signout_user'}($username, $time);
					echo "You signed out!";
					return;
				}
				$db->{'signin_user'}($username, $time, '');
				echo "You signed in!";
			}
			return;
	}
}

?>