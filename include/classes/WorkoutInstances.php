<?php
namespace Fitapp\classes;
class WorkoutInstances extends Table {

  function _construct() {
    $this->table_name = 'workout_instances';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'workout_id' => 0,
      'user_id' => '',
      'workout_date' => NULL,
      'notes' => '',
      'created'=>NULL
    ];
    $this->no_insert = ['id','created'];
    $this->no_save = ['created'];
    parent::_construct();

  }
}