<?php

class volunteermanagementsystem extends SQLite3 {

    $databasefile = 'volunteermanagement.db'

    function __construct() {
        $this->open($databasefile)
    }

    function add_volunteer($name, $username, $address, $phone, $email, $remoteaccessallowed, $under18) {
        $this->exec("INSERT INTO volunteers (name, username, address, phone, email, hours, remoteaccessallowed, under18, signedin) VALUES ($name, $username, $address, $phone, $email, $remoteaccessallowed, $under18, False)"
    }
}


?>