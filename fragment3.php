<?php

class u {
  public $id;
  public $n;
  private $a = [];

  function add_d($d) {
    $temp = new stdClass();
    $temp->n = $d['name'];
    $temp->c = [];
    foreach ($d['cards'] as $card) {
      $temp_c = new stdClass();
      $temp_c->q = $card['q'];
      $temp_c->a = $card['a'];
      array_push($temp->c, $temp_c);
    }
    array_push($this->a, $temp);
  }

  function get_d($name) {
    foreach ($this->a as $d) {
      if ($d->n == $name) return $d;
    }
  }

  function __construct($data) {
    $this->id = $data['id'];
    $this->n = $data['name'];
  }
}
