<?php
namespace Fitapp\classes;
class Workouts extends Table {

  function __construct() {
    $this->table_name = 'workouts';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'user_id' => 0,
      'create_date' => NULL,
      'workout_type' => 0,
      'filters'=>'',
      'notes'=>'',
      'created_by' => 0,
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }
}
