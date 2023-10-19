<?php

class DbOp {
  private $conn;
  public $list;

  // Connects to DB
  function connectDb($dbinfo) {
    $this->conn = mysqli_connect($dbinfo['server'], $dbinfo['user'], $dbinfo['pass'], $dbinfo['dbname']);
  }

  function getStuff($id) {
    if (empty($id)) {
        echo "Error: ID is required.";
        return [];
    } else {
        if (!$this->conn) {
            $this->connectDb([
                'server' => 'localhost',
                'user' => 'user',
                'pass' => 'password',
                'dbname' => 'database'
            ]);

            if (!$this->conn) {
                echo "Error: Unable to connect to database.";
                return [];
            } else {
                // Fetch data for the provided ID
                $res = mysqli_query($this->conn, "SELECT * FROM someTable WHERE id = " . $id); 

                if (!$res) {
                    echo "Error: Query failed.";
                    return [];
                } else {
                    $rows = [];
                    while ($row = mysqli_fetch_assoc($res)) {
                        // Check if the row is valid
                        if (isset($row['id']) && $row['id'] == $id) {
                            $rows[] = $row;
                        }
                    }

                    // Check if any rows were found
                    if (count($rows) == 0) {
                        echo "Error: No data found for ID " . $id;
                    }

                    return $rows;
                }
            }
        }
    }
}


  // Adds a card
  function add2list($item) {
    $this->list[] = $item;
  }

  function retrieve() {
    // do stuff
    for ($i = 0; $i < count($this->list); $i++) {
      echo $this->list[$i] . "<br>";
    }
  }
}
