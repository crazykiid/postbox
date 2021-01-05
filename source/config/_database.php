<?php
date_default_timezone_set('Asia/Kolkata');

class Database{

    private $conn = null;

    public function __construct(){
    }

    public function getConnection(){

        $client = new MongoDB\Client;
        $db_conn = $client->postbox_v1;
        $this->conn = $db_conn;
        return $db_conn;
    }
}
?>