<?php
namespace Fitapp\classes;
class Muscles extends Table {

  function _construct() {
    $this->table_name = 'muscles';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' : 0,
      'name' : '',
      'region' : '',
      'workout_type' : 0
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::_construct();

  }

  /**
   * Gets Muscles from DB, based on workout type, or all if none specified
   * @param Integer $workout_type
   * @return mixed Array of muscles
   */
  public function getMuscles($workout_type = NULL) {
    $filter = ($workout_type) ? 'AND workout_type=$workout_type' : '';
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}
      WHERE true {$filter}";
    $results = $this->db->CacheExecute($sql);
    $muscles = [];
    foreach ($results as $r) {
      $muscles[$r['id']]=$r;
    } 
    return $muscles;
  }

}