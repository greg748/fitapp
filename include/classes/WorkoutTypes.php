<?php
namespace Fitapp\classes;
class WorkoutTypes extends Table {

  function _construct() {
    $this->table_name = 'workout_types';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => 0,
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::_construct();

  }
}