<?php
namespace Fitapp\classes;
class Regimens extends Table {

  function __construct() {
    $this->table_name = 'regimens';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'user_id' => '',
      'name' => '',
      'regimen_type' => '', /// just text for now. Busines logic in front end
      'created' => NULL,
      'lastmodified' => NULL,
      'inactive_date' => NULL,
      'status' => 'a',
    ];

    $this->no_insert = ['id','created','lastmodified'];
    $this->no_save = ['created'];
    parent::__construct();

  }

  public static function getActiveForUser($user_id) {
    $Regimens = static::getNewSelf();
    $sql = "SELECT r.*, rw.add_date, rw.remove_date, w.id, w.name 
        FROM regimens r
        JOIN regimen_workouts rw on rw.regimen_id = rw.id and rw.status='a'
        JOIN workouts w on w.id=rw.workout_id
        WHERE r.user_id = $user_id 
        AND r.status='a'
        AND rw.add_date IS NOT NULL 
        AND NOW()::DATE between rw.add_date, coalesce(rw.remove_date, NOW()::DATE)
        ORDER BY r.lastmodified DESC, rm.added DESC";
    $regimens = [];
    $results = $Users->db->Execute($sql);
    foreach ($results as $r) {
      $regimens[$r['id']] = $r;
    }
    return $regimens;
  }

}
