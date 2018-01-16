<?php
namespace Fitapp\classes;
class Regimens extends Table {

    public static $regimenTypes = [
        'Full Body'=>[1],
        'Full Body + Cardio'=>[1,8],
        'Upper/Lower'=>[2,3],
        'Upper/Lower + Cardio'=>[2,3,8],
        'Chest/Back/Legs/Shoulders'=>[4,5,3,6],
        'Chest/Back/Legs/Shoulders + Cardio'=>[4,5,3,6,8],
        'Push/Pull/Legs/Abs'=>[4,5,3,7],
        'Push/Pull/Legs/Abs + Cardio'=>[4,5,3,7,8]
    ];

  function __construct() {
    $this->table_name = 'regimens';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'user_id' => 0,
      'created_by' => 0,
      'name' => '',
      'regimen_type' => '', /// just text for now. Business logic in front end
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
    $sql = "SELECT rw.id as regimen_workout_id, r.id as regimen_id, r.*,
        r.name as regimen_name, u.username as created_user_name, r.regimen_type,
        wt.name as workout_type, wt.id as workout_type_id, rw.add_date, rw.remove_date, w.id as workout_id, w.name as workout_name
        FROM regimens r
        JOIN regimen_workouts rw on rw.regimen_id = r.id and rw.status='a'
        LEFT JOIN workouts w on w.id=rw.workout_id
        JOIN workout_types wt on wt.id=rw.workout_type
        LEFT JOIN users u on u.id=r.created_by
        WHERE r.user_id = $user_id 
        AND r.status='a'
        AND rw.add_date IS NOT NULL 
        AND NOW()::DATE between rw.add_date AND coalesce(rw.remove_date, NOW()::DATE)
        ORDER BY r.lastmodified DESC, rw.add_date DESC";
    $regimens = [];
    $results = $Regimens->db->Execute($sql);
    if (!$results) {
        echo "<pre>";
        echo $Regimens->db->errorMsg();
        echo "<br>$sql";
        die;
    }
    foreach ($results as $r) {
      $regimens[$r['regemin_id']]['name'] = $r['regimen_name'];
      $regimens[$r['regemin_id']]['type'] = $r['regimen_type'];
      $regimens[$r['regemin_id']]['workouts'][$r['regimen_workout_id']] = $r;
    }
    return $regimens;
  }

}
