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
      wt.name as workout_type_name,
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


    public function getExercises() {
        $workout_id = $this->getField('id');
        $sql = "SELECT wei.ex_group_order, wei.group_type, wei.ex_id,
            e.name as ex_name, e.nicknames, e.primary_musc, m.name as primary_musc_name,
            e.secondary_muscs, string_agg(sm.name, ',') as secondary_musc_names,
            e.description, e.ability_level
            FROM workout_exercise_ids wei
            JOIN exercises e on e.id=wei.ex_id
            JOIN muscles m on m.id=e.primary_musc
            LEFT JOIN muscles sm on sm.id = ANY(e.secondary_muscs)
            WHERE wei.workout_id =  {$workout_id}
            GROUP BY 1,2,3,4,5,6,7,8,10,11
            ORDER BY wei.ex_group_order";
        $results = $this->db->Execute($sql);
        echo $this->db->errorMsg();
        return $results;
  }

  /**
   * Scores the workout based on muscle groups used
   *
   * @return ADORecordSet Muscles and their scores
   */
  public function getMuscleScores() {
    $workout_id = $this->getField('id');
    $sql = "SELECT muscle_name, SUM(score) AS muscle_score
        FROM exercise_muscles em
        JOIN workout_exercise_ids wei ON wei.ex_id=em.exercise_id
        WHERE wei.workout_id=$workout_id
        GROUP BY muscle_name
        ORDER BY 2 DESC";
    $results = $this->db->Execute($sql);
    return $results;
  }
  
}
