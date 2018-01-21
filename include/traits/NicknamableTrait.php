<?php

namespace Fitapp\traits; 

trait NicknamableTrait {

  /**
   * Looks to see if the item is in the list or if it's in the nickname list and returns the id if it is
   * @param String $name
   * @param Boolean $show_sql
   * @return Integer $id id of the item
   */
  public static function addIfUnique($name, $show_sql = FALSE) {
    $Static = static::getNewSelf();
    $qname = $Static->db->qstr($name);
    $sql = "SELECT id FROM {$Static->table_prefix}{$Static->table_name}
    WHERE name=$qname OR $qname = any(nicknames)";
    $id = $Static->db->GetOne($sql);
    if (!$id) {
      $NewItem = self::create(['name'=>$name], $show_sql);
      $id = $NewItem->getField('id');
    }
    return $id;
  }

    /**
   * Gets [Item] from DB all if none specified
   * @return mixed Array of [Items]
   */
  public function getAll($cache=60) {
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}
      order by name";
    $results = $this->db->CacheExecute($cache, $sql);
    $items = [];
    foreach ($results as $r) {
      $items[$r['id']]=$r;
    } 
    return $items;
  }

  public static function getMenu($clearCache = false) {
    $Object = static::getNewSelf();
    $cacheParam = ($clearCache) ? 0 : 60;
    $list = $Object->getAll($cacheParam);
    $menu = [];
    foreach ($list as $e) {
      $menu[$e['id']] = $e['name'];
    }
    return $menu;
  }

}