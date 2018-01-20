<?php
namespace Fitapp\classes;
use Fitapp\traits\NicknamableTrait;
class WeightTypes extends Table {
  use NicknamableTrait;

  function __construct() {
    $this->table_name = 'weight_types';
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

  /* @todo determine if this can be handled by trait */
  public static function getMenu($clearCache) {
    $WeightTypes = static::getNewSelf();
    $cacheParam = ($clearCache) ? 0 : 60;
    $sql = "SELECT id, name 
    FROM weight_types 
    WHERE status='a'";
    $results = $WeightTypes->db->CacheExecute($cacheParam, $sql);
    $wt = [];
    foreach ($results as $r) {
      $wt[$r['id']] = $r['name'];
    }
    return $wt;
  }

}