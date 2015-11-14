<?php

class volunteermanagementsystem extends SQLite3 {

    function __construct() {
        $this->open("./volunteermanagement.db");
    }

    function new_volunteer($name, $username, $address, $phone, $email, $remoteaccessallowed, $under18) {
        $this->exec("INSERT INTO volunteers (name, username, address, phone, email, hours, remoteaccessallowed, under18) VALUES ($name, $username, $address, $phone, $email, 0, $remoteaccessallowed, $under18)");
        return;
    }

    function new_group($name) {
        $this->exec("INSERT INTO groups (groupname, hours) VALUES ($name, 0)");
        return;
    }

    // Returns true iff $username is already within the volunteers table
    function username_exists($username) {

		$returned_set = $database->query("SELECT COUNT(*) FROM volunteers WHERE username=$username");
        while($result = $returned_set->fetchArray()) {
            $returned_set->fetchArray();
			$count = $result[0];
			if($count ==0){
				return false;
			}
        }

        return true;
    }

    // Returns true iff $username is currently signed in.
    function user_signedin($username) {
        $lasttimeid = $this->execute("SELECT signedin FROM volunteers WHERE username=$username")->fetchArray(SQLITE3_NUM)[0];
    }

    // Returns true iff $groupname is currently signed in.
    function group_signedin($groupname) {
        return $this->execute("SELECT signedin FROM groups WHERE groupname=$groupname");
    }

    function signin_user($username, $timein) {
        $userid = $this->execute("SELECT userid FROM volunteers WHERE username=$username")->fetchArray(SQLITE3_NUM)[0];
        $this->exec("INSERT INTO timesheet (timein, userid, groupsize) VALUES ($timein, $userid, 1)");
        $timeid = $this->lastInsertRowID;
        $this->exec("UPDATE volunteers SET lasttimeid=$timeid WHERE userid=$userid");
    }

    function signout_user($username, $timeout) {
        $timeid = $this->execute("SELECT lasttimeid FROM volunteers WHERE username=$username")->fetchArray(SQLITE3_NUM)[0];
        $this->exec("UPDATE timesheet SET timeout=$timeout, totaltime=$timeout-timein WHERE timeentryid=$timeid");
        $newtime = $this->execute("SELECT totaltime FROM timesheet WHERE timeentryid=$timeid")->fetchArray(SQLITE3_NUM)[0];
        $this->exec("UPDATE volunteers SET lasttimeid=Null, hours=hours+$newtime WHERE username=$username")
    }

    function signin_group($groupname, $timein) {
        $groupid = $this->execute("SELECT groupid FROM groups WHERE groupname=$groupname")->fetchArray(SQLITE3_NUM)[0];
        $this->exec("INSERT INTO timesheet (timein, groupid, groupsize) VALUES ($timein, $groupid, 1)");
        $timeid = $this->lastInsertRowID;
        $this->exec("UPDATE groups SET lasttimeid=$timeid WHERE groupid=$groupid");
    }

    function signout_group($groupname, $timeout) {
        $timeid = $this->execute("SELECT lasttimeid FROM groups WHERE groupname=$groupname")->fetchArray(SQLITE3_NUM)[0];
        $this->exec("UPDATE timesheet SET timeout=$timeout, totaltime=groupsize*($timeout-timein) WHERE timeentryid=$timeid");
        $newtime = $this->execute("SELECT totaltime FROM timesheet WHERE timeentryid=$timeid")->fetchArray(SQLITE3_NUM)[0];
        $this->exec("UPDATE groups SET lasttimeid=Null, hours=hours+$newtime WHERE groupname=$groupname")
    }

    function get_user_hours($username) {
        $hours = $this->execute("SELECT hours FROM users WHERE username=$username")->fetchArray(SQLITE3_NUM)[0];
        return array($username, $hours);
    }

    function get_all_user_hours() {
        return $this->execute("SELECT username, hours FROM users")->fetchArray(SQLITE3_NUM);
    }

    function get_group_hours($groupname) {
        $hours = $this->execute("SELECT hours FROM groups WHERE groupname=$groupname")->fetchArray(SQLITE3_NUM)[0];
        return array($groupname, $hours);
    }

    function get_all_group_hours() {
        return $this->execute("SELECT groupname, hours FROM groups")->fetchArray(SQLITE3_NUM);
    }

}

?>