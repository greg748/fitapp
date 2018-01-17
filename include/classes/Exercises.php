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
  DROP TABLE public.exercises;

CREATE TABLE public.exercises
(
    id serial primary key,
    name text COLLATE pg_catalog."default",
    nicknames text[] COLLATE pg_catalog."default",
    primary_musc integer,
    secondary_muscs integer[],
    description text COLLATE pg_catalog."default",
    ability_level integer,
   equipment integer[],
    user_position text COLLATE pg_catalog."default",
    workout_type integer[],
    grip text COLLATE pg_catalog."default",
    weight_type integer[],
    image text COLLATE pg_catalog."default",
    video text COLLATE pg_catalog."default",
    status character(1) COLLATE pg_catalog."default" DEFAULT 'a'::bpchar,
    created_by integer,
    created timestamp without time zone DEFAULT now(),
    lastmodified timestamp without time zone default now(),
    notes text COLLATE pg_catalog."default",
    stored_json json
)

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