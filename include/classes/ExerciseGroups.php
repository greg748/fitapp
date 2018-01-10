<?php
namespace Fitapp\classes;
class ExerciseGroups extends Table {

  // this is an enum type
  public static $ex_group_types = ['warmup', 'main', 'warmdown','cardio','active_recovery'];

  function __construct() {
    $this->table_name = 'ex_groups';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'group_type' => 'main',
      'exercise_ids' => [],
    ];
    $this->array_fields = ['exercise_ids'];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }

  public function addExercise($exercise_id) {
    echo "Adding $exercise_id to group";
    $current = $this->getField('exercise_ids');
    array_push($current, $exercise_id);
    print_r($current);
    $this->setField('exercise_ids', $current);
    $this->save(TRUE); //debug remove this true
  }
}