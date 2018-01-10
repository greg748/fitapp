<?php
namespace Fitapp\classes;
class ExerciseSiblings extends Table {

  function __construct() {
    $this->table_name = 'ex_siblings';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'exercise_ids' => [],
    ];
    $this->array_fields = ['exercise_ids'];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }

  // @todo
  // function makeSibling
  // function getSiblings
  // function unSibling
}