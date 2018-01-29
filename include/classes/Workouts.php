<?php
namespace Fitapp\classes;
use \Fitapp\classes\Exercises;
use \Fitapp\classes\ExerciseGroups;
use \Fitapp\traits\ScorableTrait;

class Workouts extends Table {

  use ScorableTrait;

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
     * @return mixed
     */
    public static function getAllWorkouts($filters = [], $sort = NULL) {
        $Workout = static::getNewSelf();
        $table_prefix = $Workout->table_prefix;
        $filter_text = '';
        if (count($filters)) {
            // filter the workouts
            $filter_text = "WHERE ". implode(' AND ',$filters);
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
            {$filter_text}
            ORDER BY $order";
        $results = $Workout->db->Execute($sql);
        return $results;
    }

    public static function getUserWorkouts($user_id = 0) {
        return static::getAllWorkouts(["u.id=$user_id"]);
    }

    /**
     * Returns the next group ordinal for this workout
     *
     * @return integer Next Group ordinal for this workout
     */
    public function getNextGroupOrdinal() {
        $workout_id = $this->getField('id');
        $sql = "SELECT COALESCE(MAX(group_order),0)+1
            FROM {$this->table_prefix}exercise_groups
            WHERE workout_id=$workout_id";
        return $this->db->GetOne($sql);
  }

    public function getExercises($group_id = NULL) {
        $group_filter = ($group_id > 0) ? "AND eg.id=$group_id" : '';
        $workout_id = $this->getField('id');
        $sql = "SELECT eg.group_order as exercise_group_order, eg.group_type, 
            we.exercise_id, we.exercise_order, eg.id as exercise_group_id,
            we.id as workout_exercise_id, 
            COALESCE(we.nickname_used, e.name) as exercise_name, e.primary_musc, ei.primary_muscle_name,
            e.secondary_muscs,  ei.secondary_muscle_names,
            e.description, e.ability_level, e.grip, e.user_position, ei.equipment, ei.weight_type
            FROM workout_exercises we
            JOIN exercises e on e.id = we.exercise_id
            JOIN exercise_groups eg on eg.id=we.exercise_group_id
            JOIN exercise_info ei on ei.id=e.id
            WHERE we.workout_id={$workout_id} {$group_filter}
            ORDER BY eg.group_order, we.exercise_order";
        $results = $this->db->Execute($sql);
        $exercises = [];
        $userPositions = Exercises::$userPositions;
        $gripTypes = Exercises::$gripTypes;
        $abilities = Exercises::$abilities;
        foreach ($results as $r) {
            $r['user_position'] = $userPositions[$r['user_position']];
            $r['grip'] = $gripTypes[$r['grip']];
            $r['ability_level'] = $abilities[$r['ability_level']];
            $exercises[] = $r;
        }
        return $exercises;
    }
    
    public function deleteGroup($group_id) {
        $ExerciseGroup = ExerciseGroups::get($group_id);
        $group_order = $ExerciseGroup->getField('group_order');
        if ($ExerciseGroup) {
            $sql = "DELETE FROM exercise_groups 
                WHERE id=$group_id
                CASCADE";
            $result = $this->db->Execute($sql);
            if ($result) {
                $sql = "UPDATE exercise_groups 
                    SET group_order = group_order-1
                    WHERE workout_id=$group_id  
                    AND group_order > $group_order";
                return $this->db->Execute($sql);
            }
        }
        return $result;
    }
}
