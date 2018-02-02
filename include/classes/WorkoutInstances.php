<?php
/**
 * Handles instances of a workout, also called a session on the front end
 */

namespace Fitapp\classes;
class WorkoutInstances extends Table {

    function __construct() {
        $this->table_name = 'workout_instances';
        $this->table_prefix = '';
        $this->pkey = 'id';
        $this->fields = [
            'id' => 0,
            'workout_id' => 0,
            'user_id' => '',
            'workout_date' => NULL,
            'notes' => '',
            'created'=>NULL,
            'lastmodified'=>NULL
        ];
        $this->no_insert = ['id','created','lastmodified'];
        $this->no_save = ['created','lastmodified'];
        parent::__construct();
    }

    /**
     * Returns next set ordinal value for this workout instance and exercise
     * @param $exercise_id integer
     * @return Integer
    */
    public function getNextSetOrdinal($exercise_id = 0) {
        $wi_id = $this->getField('id');
        $sql = "SELECT COALESCE(MAX(set_order),0)+1
            FROM exercise_sets
            WHERE workout_instance_id=$wi_id
            AND exercise_id=$exercise_id";
        return $this->db->getOne($sql);
    }
    
    public function getSets() {
        $wi_id = $this->getField('id');
        $sql = "SELECT * FROM exercise_sets 
        WHERE workout_instance_id=$wi_id
        ORDER by group_id, exercise_id, set_order";
        $results = $this->db->Execute($sql);
        //echo $sql;
        $sets = [];
        foreach ($results as $r) {
            $sets[$r['group_id']][$r['exercise_id']][] = $r;
        }
        return $sets;
    }

    /**
     * this.workoutInstance = {
    name : 'Upper Body 1/15/2016',
    created : '2016-01-15',
    groups : [
    {
    type: 'warmup',
    exercises: [{exercise_id: 44, name: 'push-up', sets : [{type:'single',reps: 12},{type:'bilateral', reps: 10}]},
    {exercise_id: 45, name: 'sit-up'},
    { exercise_id: 46, name: 'plank'}
    ]
    }
    , {
    type: 'main',
    exercises: [{exercise_id: 27, name: 'chin-up'},
    {exercise_id: 2, name: 'chest press'},
    { exercise_id: 18, name: 'lateral raises'}
    ]
    }]
    };
     */
    public function getWorkoutData() {
        $workout_id = $this->getField('workout_id');
        $Workout = Workouts::get($workout_id);
        $workoutData = [];
        if ($Workout) {
            $w = $Workout->getFields();
            $exercises = $Workout->getExercises();
            $sets = $this->getSets();
            $workoutData = ['name'=>$w['name'],'date'=>$this->getField('workout_date'),'notes'=>$this->getField('notes'),
                'groups'=>['group_id'=>0, 'group_type'=>'','exercises'=>[]]];
            foreach ($exercises as $e) {
                //print_r($e);
                $ex_sets = (isset($sets[$e['exercise_group_id']][$e['exercise_id']])) ?$sets[$e['exercise_group_id']][$e['exercise_id']] : [];
                $workoutData['groups'][$e['exercise_group_id']]['type'] = $e['group_type'];
                $workoutData['groups'][$e['exercise_group_id']]['group_id'] = $e['exercise_group_id'];
                $workoutData['groups'][$e['exercise_group_id']]['exercises'][$e['exercise_id']] =
                    ['exercise_id'=>$e['exercise_id'],
                        'name'=>$e['exercise_name'],
                        'sets'=>$ex_sets,
                        'other'=>$e];
            }
        }
        return $workoutData;

    }

    public function getSetsForInputs() {
        $sets = $this->getSets();
        $inputs = [];
        foreach ($sets as $set) {
            $varPrefix = "ex_{$set['group_id']}_{$set['exercise_id']}_{$set['set_order']}";
            $inputs["{$varPrefix}_type"] = $set['set_type'];
            $inputs["{$varPrefix}_weight"] = $set['weight'];
            $inputs["{$varPrefix}_units"] = $set['units'];
            $inputs["{$varPrefix}_reps"] = $set['reps'];
            $inputs["{$varPrefix}_id"] = $set['id'];
        }
        return $inputs;
    }
    
}

