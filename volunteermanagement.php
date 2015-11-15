<?php

class volunteermanagementsystem extends SQLite3 {

    function __construct($path) {
        $this->open($path);
    }

    function new_volunteer($firstname, $lastname, $username, $address, $phone, $email, $skillset, $category, $findout, $under18) {
        try {
		  $this->query("INSERT INTO volunteers (firstname, lastname, username, address, phone, email, hours, under18, skillset, category, findout) VALUES ('$firstname', '$lastname', '$username', '$address', '$phone', '$email', 0, '$under18', '$skillset', '$category', '$findout')");
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    function new_group($name, $contactname, $contactphone, $findout) {
        try {
            $this->query("INSERT INTO groups (groupname, hours, contactname, contactphone, findout) VALUES ('$name', 0, '$contactname', '$contactphone', '$findout')");
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    // Returns true iff $username is already within the volunteers table.
    function username_exists($username) {
		$result = $this->query("SELECT COUNT(*) FROM volunteers WHERE username='$username'");
		$count = $result->fetchArray()[0];
        if ($count == 0){
            return false;
        }
        return true;
    }

    // Returns true iff $email is already within the volunteers table.
    function email_exists($email) {
        $result = $this->query("SELECT COUNT(*) FROM volunteers WHERE email='$email'");
        $count = $result->fetchArray()[0];
        if ($count == 0){
            return false;
        }
        return true;
    }

    // Returns true iff $groupname is already within the groups table.
    function groupname_exists($groupname) {
        $result = $this->query("SELECT COUNT(*) FROM groups WHERE groupname='$groupname'");
        $count = $result->fetchArray()[0];
        if ($count == 0){
            return false;
        }
        return true;
    }

    // Returns true iff $username is currently signed in.
    function user_signedin($username) {
        $result = $this->query("SELECT lasttimeid FROM volunteers WHERE username='$username'");
        $lasttimeid = $result->fetchArray()[0];
        if ($lasttimeid == null) {
            return false;
        }
        return true;
    }

    // Returns true iff $groupname is currently signed in.
    function group_signedin($groupname) {
        $result = $this->query("SELECT lasttimeid FROM groups WHERE groupname='$groupname'");
        $lasttimeid = $result->fetchArray()[0];
        if ($lasttimeid == null) {
            return false;
        }
        return true;
    }

    function signin_user($username, $timein) {
        $result = $this->query("SELECT userid FROM volunteers WHERE username='$username'");
        $userid = $result->fetchArray()[0];
        $this->query("INSERT INTO timesheet (timein, userid, groupsize) VALUES ('$timein', '$userid', 1)");
        $result = $this->query("SELECT timeentryid FROM timesheet WHERE timein = '$timein' AND userid= '$userid'");
		$timeid = $result->fetchArray()[0];
        $this->query("UPDATE volunteers SET lasttimeid='$timeid' WHERE userid='$userid'");
		return $timeid;
	}

    function signout_user($username, $timeout, $comments) {
        $result = $this->query("SELECT lasttimeid FROM volunteers WHERE username='$username'");
        $timeid = $result->fetchArray()[0];
        $this->query("UPDATE timesheet SET timeout='$timeout', totaltime='$timeout'-timein, comments='$comments' WHERE timeentryid='$timeid'");
        $result = $this->query("SELECT totaltime FROM timesheet WHERE timeentryid='$timeid'");
        $newtime = $result->fetchArray()[0];
        $this->query("UPDATE volunteers SET lasttimeid=Null, hours=hours+'$newtime' WHERE username='$username'");
		return $newtime;
    }

    function signin_group($groupname, $timein, $groupsize) {
        $result = $this->query("SELECT groupid FROM groups WHERE groupname='$groupname'");
        $groupid = $result->fetchArray()[0];
        $this->query("INSERT INTO timesheet (timein, groupid, groupsize) VALUES ('$timein', '$groupid', '$groupsize')");
        $result = $this->query("SELECT timeentryid FROM timesheet WHERE timein = '$timein' AND groupid= '$groupid'");
		$timeid = $result->fetchArray()[0];
        $this->query("UPDATE groups SET lasttimeid='$timeid' WHERE groupid='$groupid'");
		return $timeid;
	}

    function signout_group($groupname, $timeout, $comments) {
        $result = $this->query("SELECT lasttimeid FROM groups WHERE groupname='$groupname'");
        $timeid = $result->fetchArray()[0];
        $this->query("UPDATE timesheet SET timeout='$timeout', totaltime=groupsize*('$timeout'-timein)/3600., comments='$comments' WHERE timeentryid='$timeid'");
        $result = $this->query("SELECT totaltime FROM timesheet WHERE timeentryid='$timeid'");
        $newtime = $result->fetchArray()[0];
        $this->query("UPDATE groups SET lasttimeid=Null, hours=hours+'$newtime' WHERE groupname='$groupname'");
		return $newtime;
    }

    function get_user_hours($firstname, $lastname) {
        $result = $this->query("SELECT hours FROM volunteers WHERE firstname='$firstname' AND lastname='$lastname'");
        $hours = $result->fetchArray()[0];
        return array($firstname, $lastname, $hours);
    }

    function get_group_hours($groupname) {
        $result = $this->query("SELECT hours FROM groups WHERE groupname='$groupname'");
        $hours = $result->fetchArray()[0];
        return array($groupname, $hours);
    }

    // Returns a table of volunteers, phone numbers, and their total hours worked between startdate and enddate.
    function get_aggregate_user_hours($startdate=0, $enddate=9999999999) {
        $result = $this->query("SELECT volunteers.firstname, volunteers.lastname, volunteers.phone, sum(timesheet.totaltime) FROM timesheet INNER JOIN volunteers ON timesheet.userid=volunteers.userid WHERE timesheet.timein>'$startdate' AND timesheet.timeout<'$enddate' AND timesheet.groupid IS NULL GROUP BY volunteers.username");
        return $result->fetchArray();
    }

    // Returns a table of groups, contact names, contact phone numbers, and their total hours worked between a start date and end date.
    function get_aggregate_group_hours($startdate=0, $enddate=9999999999) {
        $result = $this->query("SELECT groups.groupname, groups.contactname, groups.contactphone, sum(timesheet.totaltime) FROM timesheet INNER JOIN groups ON timesheet.groupid=groups.groupid WHERE timesheet.timein>'$startdate' AND timesheet.timeout<'$enddate' AND timesheet.userid IS NULL GROUP BY groups.groupname");
        return $result->fetchArray();
    }

    // Returns a timesheet for a user between a start date and end date.
    function get_user_timesheet($firstname, $lastname, $startdate=0, $enddate=9999999999) {
        $result = $this->query("SELECT userid FROM volunteers WHERE volunteers.firstname='$firstname' AND volunteers.lastname='$lastname'");
        $userid = $result->fetchArray()[0];
        $result = $this->query("SELECT timein, totaltime FROM timesheet WHERE timein>'$startdate' AND timeout<'$enddate' AND userid='$userid'");
        $curr = $result->fetchArray();
        $arr = array();
        $i = 0;
        while ($curr != false) {
            $utc_date = DateTime::createFromFormat('U', $curr[0]);  
            $new_date = $utc_date->setTimezone(new DateTimeZone('America/New_York'));
            $arr[$i] = array($new_date->format('Y-m-d'), $curr[1]);
            $curr = $result->fetchArray();
            $i++;
        }
        return $arr;
    }

    // Returns a timesheet for a group between a start date and end date.
    function get_group_timesheet($groupname, $startdate=0, $enddate=9999999999) {
        $result = $this->query("SELECT groupid FROM groups WHERE groupname='$groupname'");
        $groupid = $result->fetchArray()[0];
        $result = $this->query("SELECT datetime(timein, 'unixepoch'), totaltime FROM timesheet WHERE timein>'$startdate' AND timeout<'$enddate' AND groupid='$groupid'");
        $curr = $result->fetchArray();
        $arr = array();
        $i = 0;
        while ($curr != false) {
            $utc_date = DateTime::createFromFormat('Y-m-d H:i:s', $curr[0], new DateTimeZone('UTC'));  
            $new_date = $utc_date->{'setTimeZone'}(new DateTimeZone('America/New_York'));
            $arr[$i] = array($new_date->format('Y-m-d'), $curr[1]);
            $curr = $result->fetchArray();
            $i++;
        }
        return $arr;
    }

    // Returns all of the info on a particular person.
    function get_user_info($firstname, $lastname) {
        $result = $this->query("SELECT * FROM volunteers WHERE firstname='$firstname' AND lastname='$lastname'");
        return $result->fetchArray();
    }

    // Returns all of the info on a particular group.
    function get_group_info($groupname) {
        $result = $this->query("SELECT * FROM groups WHERE groupname='$groupname'");
        return $result->fetchArray();
    }

}

?>