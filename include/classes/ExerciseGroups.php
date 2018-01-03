<?php
namespace Fitapp\classes;
class ExerciseGroups extends Table {

  function _construct() {
    $this->table_name = 'ex_groups';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'group_type' => 0,
      'exercise_ids' => [],
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::_construct();

  }
}