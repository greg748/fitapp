<?php
namespace Fitapp\classes;
use Fitapp\classes\AppConfig;

class Exercises extends Table {

  public static $userPositions = ['standing','bench','kneeling','half-kneeling','seated','incline bench', 'lying', 'lying knees at 90', 'lying legs at 45',
      'decline bench','roman chair','back extension','prone','plank','high plank'];
  public static $gripTypes = ['neutral','palm-in','palm-up','thumb-up','thumb-down', 'thumb-up-and-in','reverse','wide','narrow','hammer','foot','med-ball','multi'];
  public static $abilities = [1=>'beginner',2=>'basic',3=>'moderate',4=>'advanced',5=>'expert'];

  function __construct() {
    $this->table_name = 'exercises';
    $this->table_prefix = '';
    $this->pkey = 'id';
    $this->fields = [
      'id' => 0,
      'name' => '',
      'nicknames' => '',
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
      'created' => NULL // @todo add lastmodified?
    ];
    $this->array_fields = ['secondary_muscs','equipment','workout_type','weight_type'];
    $this->no_insert = ['id', 'created'];
    $this->no_save = ['created'];
    parent::__construct();

  }

  public static function display($id) {
    $Config = AppConfig::get();
    $adminDir = $Config->getAppDir().'/admin';
    $e = [];
    if (is_array($id)) {
      $e = $id;
    } else {
      $Exercise = static::get($id);
      if ($Exercise) {
        $e = $Exercise->getFields();
      }
    }
    include "$adminDir/exercises/display.php";
  }

  /**
   * Gets Exercises from DB, based on filters, or all if none specified
   * @param mixed $filters
   * @return mixed Array of exercises
   */
  public function getExercises($filters = NULL) {
    $filters = $this->filterExercises($filters);
    $sql = "SELECT * from
      {$this->table_prefix}{$this->table_name}
      WHERE true {$filters}";
    $results = $this->db->CacheExecute($sql);
    $exercises = [];
    foreach ($results as $r) {
      $exercises[$r['id']]=$r;
    } 
    return $exercises;
  }

  /**
   * Builds out sql where clauses based on filters assigned
   * @param mixed $filters facets to filter on
   * @return string SQL to filter on these facets
   */
  protected function filterExercises($filters = NULL) {
    $exFitlers = '';
    return $exFilters;
  }

}