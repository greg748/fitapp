<?php
namespace Fitapp\classes;
class WorkoutExercises extends Table {

  function __construct() {
    $this->table_name = 'workout_exercises';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'exercise_id'=>0,
      'workout_id' => 0,
      'exercise_group_id' => 0,
      'exercise_order' => 1,
      'nickname_used' => NULL,
      'rep_pattern'=> [12,10,8],
    ];
    $this->no_insert = ['id'];
    $this->null_fields = ['nickname_used'];
    $this->array_fields = ['rep_pattern'];
    $this->no_save = [];
    parent::__construct();

  }
  
}