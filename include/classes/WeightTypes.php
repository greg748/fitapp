<?php
namespace Fitapp\classes;
class WeightTypes extends Table {

  function __construct() {
    $this->table_name = 'weight_types';
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
   * Gets Equipment from DB, based on workout type, or all if none specified
   * @return mixed Array of weight types
   */
  public function getWeightTypes() {
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}
      ";
    $results = $this->db->CacheExecute($sql);
    $weight_types = [];
    foreach ($results as $r) {
      $weight_types[$r['id']]=$r;
    } 
    return $weight_types;
  }

  public static function getWeightTypesMenu() {
    $WeightTypes = static::getNewSelf();
    $sql = "SELECT id, name FROM weight_types 
    WHERE status='a'";
    echo "$sql";
    $results = $WeightTypes->db->CacheExecute(7200, $sql);
    $wt = [];
    foreach ($results as $r) {
      $wt[$r['id']] = $r['name'];
    }
    print_r($wt);
    return $wt;
  }

}