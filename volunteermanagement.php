<?php

class volunteermanagementsystem extends SQLite3 {

    $databasefile = 'volunteermanagement.db'

    function __construct() {
        $this->open($databasefile)
    }

    function new_volunteer($name, $username, $address, $phone, $email, $remoteaccessallowed, $under18) {
        $this->exec("INSERT INTO volunteers (name, username, address, phone, email, hours, remoteaccessallowed, under18) VALUES ($name, $username, $address, $phone, $email, 0, $remoteaccessallowed, $under18)");
        return;
    }

    function new_group($name) {
        $this->exec("INSERT INTO groups (groupname, hours) VALUES ($name, 0)");
        return;
    }

    // Note that groupbool is true for groups, false for individuals.
    // Returns a timeentryid number (to associate with a timeout).
    function new_timein($timein, $user, $groupsize, $name, $groupbool) {
        if $groupbool {
            $groupid = $this->execute("SELECT groupid FROM groups WHERE groupname=$name");
            $this->exec("INSERT INTO timesheet (timein, groupsize, groupid) VALUES ($timein, $groupsize, $groupid)");
        }
        else {
            $userid = $this->execute("SELECT userid FROM volunteers WHERE username=$name");
            $this->exec("INSERT INTO timesheet (timein, userid, groupsize) VALUES ($timein, $userid, 1)");
        }
        return $this->lastInsertRowID;
    }

    // Stamps a timeout $timeout onto a time entry designated by $timeid.
    function new_timeout($timeout, $timeid) {
        $this->exec("UPDATE timesheet SET timeout=$timeout, totaltime=groupsize*($timeout-timein) WHERE timeentryid=$timeid");
        return;
    }

    // Returns true iff $username is already within the volunteers table
    function username_exists($username) {
        if $this->execute("SELECT COUNT(*) FROM volunteers WHERE username=$username")->fetchArray(SQLITE3_NUM)[0] == 0 {
            return false;
        }
        return true;
    }

    // Returns true iff $username is currently signed in.
    function user_signedin($username) {
        return $this->execute("SELECT signedin FROM volunteers WHERE username=$username")->fetchArray(SQLITE3_NUM)[0];
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
        $this->exec("UPDATE timesheet SET timeout=$timeout, totaltime=groupsize*($timeout-timein) WHERE timeentryid=$timeid");
        $this->exec("UPDATE volunteers SET lasttimeid=Null WHERE username=$username")
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
        $this->exec("UPDATE groups SET lasttimeid=Null WHERE groupname=$groupname")
    }

}


?>