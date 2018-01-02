<?php
namespace Fitapp\classes;
class WorkoutItems extends Table {

  function __construct() {
    $this->table_name = 'workout_items';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'workout_id' => 0,
      'ex_group_id' => 0,
      'ex_group_order' => 0,
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }
}