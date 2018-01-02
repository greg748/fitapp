<?php
namespace Fitapp\classes;
class Muscles extends Table {

  function __construct() {
    $this->table_name = 'muscles';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'region' => '',
      'workout_type' => 0
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }

  /**
   * Gets Muscles from DB, based on workout type, or all if none specified
   * @param Integer $workout_type
   * @return mixed Array of muscles
   */
  public static function getMuscles($workout_type = NULL) {
    $Muscle = static::getNewSelf();
    $filter = ($workout_type) ? "AND workout_type=$workout_type" : '';
    $sql = "SELECT * from
      {$Muscle->table_prefix}{$Muscle->table_name}
      WHERE true {$filter}";
      echo $sql;
    $results = $Muscle->db->CacheExecute($sql);
    $muscles = [];
    foreach ($results as $r) {
      $muscles[$r['id']]=$r;
    } 
    return $muscles;
  }

}