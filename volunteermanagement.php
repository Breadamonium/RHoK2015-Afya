<?php

class volunteermanagementsystem extends SQLite3 {

    function __construct() {
        $this->{open}("./volunteermanagement.db");
    }

    function new_volunteer($name, $username, $address, $phone, $email, $remoteaccessallowed, $under18) {
        $this->{query}("INSERT INTO volunteers (name, username, address, phone, email, hours, remoteaccessallowed, under18) VALUES ($name, $username, $address, $phone, $email, 0, $remoteaccessallowed, $under18)");
        return;
    }

    function new_group($name) {
        $this->{query}("INSERT INTO groups (groupname, hours) VALUES ('$name', 0)");
        return;
    }

    // Returns true iff $username is already within the volunteers table
    function username_exists($username) {
		$result = $this->{query}("SELECT COUNT(*) FROM volunteers WHERE username='$username'");
		$count = $result->{fetchArray}()[0];
        if ($count == 0){
            return false;
        }
        return true;
    }

    // Returns true iff $username is currently signed in.
    function user_signedin($username) {
        $result = $this->{query}("SELECT signedin FROM volunteers WHERE username='$username'");
        $lasttimeid = $result->{fetchArray}()[0];
        if ($lasttimeid == Null) {
            return false;
        }
        return true;
    }

    // Returns true iff $groupname is currently signed in.
    function group_signedin($groupname) {
        $result = $this->{query}("SELECT signedin FROM groups WHERE groupname='$groupname'");
        $lasttimeid = $result->{fetchArray}()[0];
        if ($lasttimeid == Null) {
            return false;
        }
        return true;
    }

    function signin_user($username, $timein) {
        $result = $this->{query}("SELECT userid FROM volunteers WHERE username='$username'");
        $userid = $result->{fetchArray}()[0];
        $this->{query}("INSERT INTO timesheet (timein, userid, groupsize) VALUES ('$timein', '$userid', 1)");
        $timeid = $this->{lastInsertRowID};
        $this->{query}("UPDATE volunteers SET lasttimeid='$timeid' WHERE userid='$userid'");
    }

    function signout_user($username, $timeout) {
        $result = $this->{query}("SELECT lasttimeid FROM volunteers WHERE username='$username'");
        $timeid = $result->{fetchArray}()[0];
        $this->{query}("UPDATE timesheet SET timeout='$timeout', totaltime='$timeout'-timein WHERE timeentryid='$timeid'");
        $result = $this->{query}("SELECT totaltime FROM timesheet WHERE timeentryid='$timeid'");
        $newtime = $result->{fetchArray}()[0];
        $this->{query}("UPDATE volunteers SET lasttimeid=Null, hours=hours+'$newtime' WHERE username='$username'")
    }

    function signin_group($groupname, $timein) {
        $result = $this->{query}("SELECT groupid FROM groups WHERE groupname='$groupname'");
        $groupid = $result->{fetchArray}()[0];
        $this->{query}("INSERT INTO timesheet (timein, groupid, groupsize) VALUES ('$timein', '$groupid', 1)");
        $timeid = $this->{lastInsertRowID};
        $this->{query}("UPDATE groups SET lasttimeid='$timeid' WHERE groupid='$groupid'");
    }

    function signout_group($groupname, $timeout) {
        $result = $this->{query}("SELECT lasttimeid FROM groups WHERE groupname='$groupname'");
        $timeid = $result->{fetchArray}()[0];
        $this->{query}("UPDATE timesheet SET timeout='$timeout', totaltime=groupsize*('$timeout'-timein) WHERE timeentryid='$timeid'");
        $result = $this->{query}("SELECT totaltime FROM timesheet WHERE timeentryid='$timeid'");
        $newtime = $result->{fetchArray}()[0];
        $this->{query}("UPDATE groups SET lasttimeid=Null, hours=hours+'$newtime' WHERE groupname='$groupname'")
    }

    function get_user_hours($username) {
        $result = $this->{query}("SELECT hours FROM users WHERE username='$username'");
        $hours = $result->{fetchArray}()[0];
        return array($username, $hours);
    }

    function get_all_user_hours() {
        $result = $this->{query}("SELECT username, hours FROM users");
        return $result->{fetchArray}();
    }

    function get_group_hours($groupname) {
        $result = $this->{query}("SELECT hours FROM groups WHERE groupname='$groupname'");
        $hours = $result->{fetchArray}()[0];
        return array($groupname, $hours);
    }

    function get_all_group_hours() {
        $result = $this->{query}("SELECT groupname, hours FROM groups");
        return $result->{fetchArray}();
    }

}

?>