<?php
namespace Fitapp\classes;
use Fitapp\traits\NicknamableTrait;

class ExerciseClassifiers extends Table {
  use NicknamableTrait;

  function __construct() {
    $this->table_name = 'exercise_classifiers';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'nicknames' => [],
      'status' => 'a',
      'created' => null,
    ];
    $this->array_fields = ['nicknames'];
    $this->no_insert = ['id', 'created'];
    $this->no_save = ['created'];
    parent::__construct();

  }

}