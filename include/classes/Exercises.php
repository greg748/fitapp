<?php
namespace Fitapp\classes;
use Fitapp\classes\AppConfig;

class Exercises extends Table {

    public static $userPositions = ['standing','bench','kneeling','half-kneeling','seated','incline bench', 'lying', 'lying knees at 90', 'lying legs at 45',
        'side lying', 'decline bench','physio head and shoulders','physio chest','squatted','stagger stance','lunge','prone','plank','high plank','side plank','high side plank','roman chair','back extension'];
    public static $gripTypes = ['neutral','palm-in','palm-up','thumb-up','thumb-down', 'thumb-up-and-in','reverse','wide','narrow',
        'hammer','foot','med-ball','multi', 'v-grip'];
    public static $abilities = [1=>'beginner',2=>'basic',3=>'moderate',4=>'advanced',5=>'expert'];
    public static $abilityMultipliers = [1=>1, 2=>1, 3=>1.5, 4=>1.75, 5=>2];

  function __construct() {
    $this->table_name = 'exercises';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'nicknames' => [],
      'primary_musc' => 0,
      'secondary_muscs' => [],
      'description' => '',
      'ability_level' => 1,
      'equipment' => [],
      'user_position' => '', // $userPositions
      'workout_type' => [],
      'grip' => '',
      'weight_type' => [], 
      'image' => '',
      'video' => '',
      'status' => 'a',
      'created_by' => 0,
      'notes' => '',
      'created' => NULL, 
      'lastmodified' => NULL,
      'stored_json' => NULL, // will be used to store a cached version of the exercise
    ];
    $this->array_fields = ['secondary_muscs','equipment','workout_type','weight_type','nicknames'];
    $this->no_insert = ['id', 'created','lastmodified'];
    $this->no_save = ['created','lastmodified'];
    parent::__construct();

  }

  public static function display($exercise) {
    $Config = AppConfig::get();
    $adminDir = $Config->getAppDir().'/admin';
    if (!is_array($exercise)) {
      $Exercise = static::get($exercise);
      if ($Exercise) {
        $exercise = $Exercise->getFields();
      }
    }
    echo <<<DISP
    <div class="exercise">
<div class="exerciseName"><a href="/admin/exercises/edit.php?id={$exercise['exercise_id']}">{$exercise['exercise_name']}</a></div>
<div class="exerciseDesc">{$exercise['description']}</div>
<div class="primaryMuscle">{$exercise['primary_muscle_name']}</div>
<div class="secondaryMuscles">{$exercise['secondary_muscle_names']}</div>
<div class="equipment">{$exercise['equipment']}</div>
<div class="weightType">{$exercise['weight_type']}</div>
<div class="grip">{$exercise['grip']}</div>
<div class="user_position">{$exercise['user_position']}</div>
<div class="ability">{$exercise['ability_level']}</div>
</div>
DISP;
  }

  public static function getExercisesMenu($filters = NULL) {
    $Exercises = static::getNewSelf();
    $filters = $Exercises->filterExercises($filters);
    $sql = "SELECT id, name FROM exercises
        WHERE true
        {$filters}";
    $results = $Exercises->db->Execute($sql);
    echo $Exercises->db->errorMsg();
    print_pre($sql);
    $exercises = [];
    foreach ($results as $r) {
        $exercises[$r['id']]= $r['name'];
    }
    return $exercises;
  }



   /**
   * Gets Exercises from DB, based on filters, or all if none specified
   * @param mixed $filters
   * @return mixed Array of exercises
   */
  public static function getExercises($filters = NULL) {
      $Exercises = static::getNewSelf();
      $filters = $Exercises->filterExercises($filters);
      $sql = "SELECT eg.group_order as exercise_group_order, eg.group_type, we.exercise_id, eg.id as exercise_group_id,
      coalesce(we.nickname_used, e.name) as exercise_name, e.primary_musc, ei.primary_muscle_name,
      e.secondary_muscs,  ei.secondary_muscle_names,
      e.description, e.ability_level, e.grip, e.user_position, ei.equipment, ei.weight_type
      FROM workout_exercises we
      JOIN exercises e on e.id = we.exercise_id
      JOIN exercise_groups eg on eg.id=we.exercise_group_id
      JOIN exercise_info ei on ei.id=e.id
      WHERE e.status='a'
      {$filters}
      ORDER BY eg.group_order, we.exercise_order";
    $results = $Exercises->db->Execute($sql);
    echo $Exercises->db->errorMsg();
    print_pre($sql);
    $exercises = [];
    $userPositions = Exercises::$userPositions;
    $gripTypes = Exercises::$gripTypes;
    $abilities = Exercises::$abilities;
    foreach ($results as $r) {
        $r['user_position'] = $userPositions[$r['user_position']];
        $r['grip'] = $gripTypes[$r['grip']];
        $r['ability_level'] = $abilities[$r['ability_level']];
        $exercises[$r['exercise_id']]=$r;
    } 
    return $exercises;
  }

    /**
     * Builds out sql where clauses based on filters assigned
     * @param mixed $filters facets to filter on
     * @return string SQL to filter on these facets
     */
    protected function filterExercises($filters = NULL) {
        $exFilters = [];
        foreach ($filters as $f=>$val) {
            switch ($f) {
                case 'workout_type' :
                    $exFilters[] = "$val = ANY($f)";
                    break;
                default : 
                    $exFilters[] = "$f = '$val'";
            }
        }

        return (count($exFilters) > 0) ? 'AND '. implode("\nAND ",$exFilters) : '';
    }

}