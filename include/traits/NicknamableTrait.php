<?php

namespace Fitapp\traits; 

trait NicknamableTrait {

  /**
   * Looks to see if the item is in the list or if it's in the nickname list and returns the id if it is
   * @param String $name
   * @return Integer $id id of the item
   */
  public static function addIfUnique($name) {
    $name = $db->qstr($name);
    $sql = "SELECT id FROM {$this->table_prefix}{$this->table_name}
    WHERE name=$name OR $name = any(nickname)";
    $id = $db->GetOne($sql);
    if (!$id) {
      $NewItem = self::create(['name'=>$name]);
      $id = $NewItem->getField('id');
    }
    return $id;
  }

}