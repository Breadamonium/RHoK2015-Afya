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
	switch ($name) {
		case 'get_user_hours':
		case 'get_user_info':
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			echo json_encode($db->{$name}($firstname, $lastname));
			return;
		case 'get_group_hours':
			$groupname = $_POST['groupname'];
			echo json_encode($db->{$name}($groupname));
			return;
		case 'get_aggregate_user_hours':
		case 'get_aggregate_group_hours':
			$startdate = $_POST['startdate'];
			$enddate = $_POST['enddate'];
			echo json_encode($db->{$name}($startdate, $enddate));
			return;
		case 'get_user_timesheet':
			$firstname = $_POST['firstname'];
			$lastname = $_POST['lastname'];
			$startdate = $_POST['startdate'];
			$enddate = $_POST['enddate'];
			echo json_encode($db->{$name}($firstname, $lastname, $startdate, $enddate));
			return;
		case 'get_group_timesheet':
			$groupname = $_POST['groupname'];
			$startdate = $_POST['startdate'];
			$enddate = $_POST['enddate'];
			echo json_encode($db->{$name}($groupname, $startdate, $enddate));
			return;
	}
}





?>