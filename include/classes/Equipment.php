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
    $this->array_fields = ['nicknames'];
    $this->no_insert = ['id', 'created'];
    $this->no_save = ['created'];
    parent::__construct();

  }

  /**
   * Gets Equipment from DB all if none specified
   * @return mixed Array of Equipment
   */
  public function getEquipment($cache=60) {
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}";
    $results = $this->db->CacheExecute($cache, $sql);
    $equipment = [];
    foreach ($results as $r) {
      $equipment[$r['id']]=$r;
    } 
    return $equipment;
  }

  public static function getEquipmentMenu($clearCache = false) {
    $Equipment = static::getNewSelf();
    $cacheParam = ($clearCache) ? 0 : 60;
    $equipList = $Equipment->getEquipment($cacheParam);
    $menu = [];
    foreach ($equipList as $e) {
      $menu[$e['id']] = $e['name'];
    }
    return $menu;
  }

}