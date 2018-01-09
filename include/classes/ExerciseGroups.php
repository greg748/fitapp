<?php
namespace Fitapp\classes;
class ExerciseGroups extends Table {

  // this is an enum type
  public static $ex_group_types = ['warmup', 'main', 'warmdown','cardio','active_recovery'];

  function _construct() {
    $this->table_name = 'ex_groups';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'group_type' => 0,
      'exercise_ids' => [],
    ];
    $this->array_fields = ['exercise_ids'];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::_construct();

  }
}