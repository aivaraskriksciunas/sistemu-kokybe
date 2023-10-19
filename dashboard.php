<?php

class dashboard {
    public $info_list;

    public function render_list() {
        foreach ($this->info_list as $y) {
            echo $y->n . ": ";
            foreach ($y->c as $z) {
                echo $z->q . " -> " . $z->a . ", ";
            }
            echo "<br>";
        }
    }

    function get_data_list($q) {
        $temp = [];
        foreach ($this->info_list as $y) {
            foreach ($y->c as $z) {
                if (strpos($z->q, $q) !== false) {
                    array_push($temp, $z);
                }
            }
        }
        return $temp;
    }

    function __construct($info_list) {
        $this->info_list = $info_list;
    }
}

