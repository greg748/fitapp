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
    $current = array_filter($this->getField('exercise_ids'));
    if (count($current)) {
      array_push($current, $exercise_id);
    } else {
      $current = [$exercise_id];
    }
    $this->setField('exercise_ids', $current);
    $this->save(); 
    if (!$this->isSaved()) {
      echo "<pre>Error\n".$this->lastSql()."\n".$this->errorMsg()."</pre>";
      die;
    }
  }
}