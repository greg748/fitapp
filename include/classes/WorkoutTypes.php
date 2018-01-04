<?php
namespace Fitapp\classes;
class WorkoutTypes extends Table {

  function __construct() {
    $this->table_name = 'workout_types';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => 0,
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }

  public static function getWorkoutTypes() {
    $WorkoutTypes = static::getNewSelf();
    $sql = "SELECT id, name FROM workout_types";
    $results = $WorkoutTypes->db->CacheExecute(7200, $sql);
    $wt = [];
    foreach ($results as $r) {
      $wt[$r['id']] = $r['name'];
    }
    return $wt;
  }
}