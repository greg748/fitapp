<?php
namespace Fitapp\classes;
use Fitapp\traits\NicknamableTrait;

class Equipment extends Table {
  use NicknamableTrait;

  function __construct() {
    $this->table_name = 'equipment';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'nicknames' => [],
      'status' => 'a',
      'created' => null,
    ];
    $this->no_insert = ['id', 'created'];
    $this->no_save = ['created'];
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

  public static function getEquipmentMenu() {
    $Equipment = static::getNewSelf();
    $equipList = $Equipment->getEquipment();
    $menu = [];
    foreach ($equipList as $e) {
      $menu[$e['id']] = $e['name'];
    }
    return $menu;
  }

}