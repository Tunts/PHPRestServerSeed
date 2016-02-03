<?php

namespace Tunts\Persistency;

use mysqli;

class SimpleMySQL {

    protected $connection;

    function __construct($server, $user, $pass, $database) {
        $mysqli = new mysqli($server, $user, $pass, $database);

        if ($mysqli->connect_error) {
            $this->connection = $mysqli;
            die($this->getErr());
        } else {
            $this->connection = $mysqli;
        }
    }

    function __destruct() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    function query($query) {
        return $this->connection->query($query);
    }

    function escape($string) {
        return $this->connection->real_escape_string($string);
    }

    function getErr() {
        return "Connect Error: " . $this->connection->errno . " - " . $this->connection->error;
    }

    function getLastId() {
        return $this->connection->insert_id;
	}
}

?>