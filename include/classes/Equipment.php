<?php
namespace Fitapp\classes;
class Equipment extends Table {

  function _construct() {
    $this->table_name = 'equipment';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' : 0,
      'name' : '',
      'nicknames' : '',
      'status' : 'a'
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::_construct();

  }

  /**
   * Gets Equipment from DB, based on workout type, or all if none specified
   * @return mixed Array of Equipment
   */
  public function getEquipment() {
    $filter = ($workout_type) ? 'AND workout_type=$workout_type' : '';
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}
      WHERE true {$filter}";
    $results = $this->db->CacheExecute($sql);
    $equipment = [];
    foreach ($results as $r) {
      $equipment[$r['id']]=$r;
    } 
    return $equipment;
  }

}