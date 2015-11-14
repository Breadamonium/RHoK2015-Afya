<?php

$databasefile = 'volunteermanagement.db'
$open = false
$dbconn = null

class volunteermanagementsystem extends SQLite3 {
    function open_db() {
        if $open {
            return;
        }
        $dbconn = new SQLite3($databasefile);
        $open = true;
        return;
    }

    function close_db() {
        if !$open {
            return;
        }
        $dbconn->close();
        $open = false;
    }

    function add_volunteer($name, $username, $address, $phone, $email, $remoteaccessallowed) {
        if !$open {
            open_db();
        }


    }
}


?>