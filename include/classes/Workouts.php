<?php
namespace Fitapp\classes;
class Workouts extends Table {

  function __construct() {
    $this->table_name = 'workouts';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'user_id' => 0,
      'create_date' => NULL,
      'workout_type' => 0,
      'filters'=>'',
      'notes'=>'',
      'created_by' => 0,
    ];
    $this->no_insert = ['id'];
    $this->no_save = [];
    parent::__construct();

  }

  /**
   * Retrieve workouts based on a filters and sort
   *
   * @param array $filters
   * @param String $sort
   * @return ADORecordSet 
   */
  public static function getAllWorkouts($filters = [], $sort = NULL) {
    $Workout = static::getNewSelf();
    $table_prefix = $Workout->table_prefix;
    if (count($filters)) {
      // filter the workouts
    }
    $order = ($sort)?? 'create_date DESC';
    $sql = "SELECT w.*, 
      wt.name as workout_type_name
      u.username, u.firstname, u.lastname, 
      c.username as cb_username, c.firstname as cb_firstname, c.lastname as cb_lastname 
      FROM {$table_prefix}workouts w
      JOIN {$table_prefix}workout_types wt on wt.id=w.workout_type
      LEFT JOIN {$table_prefix}users u on u.id=w.user_id
      LEFT JOIN {$table_prefix}users c on c.id=w.created_by
      ORDER BY $order";
    $results = $Workout->db->Execute($sql);
    return $results;
  }
  
}
