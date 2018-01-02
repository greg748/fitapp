<?php
namespace Fitapp\classes;
class Equipment extends Table {

  function __construct() {
    $this->table_name = 'equipment';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'nicknames' => '',
      'status' => 'a'
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }

  /**
   * Gets Equipment from DB all if none specified
   * @return mixed Array of Equipment
   */
  public function getEquipment() {
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}";
    $results = $this->db->CacheExecute($sql);
    $equipment = [];
    foreach ($results as $r) {
      $equipment[$r['id']]=$r;
    } 
    return $equipment;
  }

}