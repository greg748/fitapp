<?php
include_once '../init.php';
use Fitapp\classes\Workouts;
use Fitapp\classes\Users;
use Fitapp\classes\WorkoutInstances;
use Fitapp\classes\Sets;
use Fitapp\tools\Template;

if (isset($_REQUEST['id'])) {
    $id = $_REQUEST['id'];
    $_SESSION['workout_id'] = $id;
} elseif (isset($_SESSION['workout_id'])) {
    $id = $_SESSION['workout_id'];
}


$Workout = Workouts::get($id);
$w = $Workout->getFields();
if (isset($_REQUEST['createInstance'])) {
    $data = [
        'workout_id' => $w['id'],
        'user_id' => $w['user_id'],
        'workout_date' => date('Y-m-d'),
    ];
    $WorkoutInstance = WorkoutInstances::create($data, TRUE);
    $wi = $WorkoutInstance->getFields();
    $wi_id = $wi['id'];
    $_SESSION['workout_instance'] = $wi_id;
} else {
    $wi_id = $_SESSION['workout_instance'];
}
$exercises = $Workout->getExercises();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    print_pre($_POST);
    die;
}

Template::startPage('Workout Instance');
if (!$WorkoutInstance) {
    echo "<a href=\"?createInstance=true\">Create Instance</a>";
}
$group_id = NULL;
foreach ($exercises as $e) {
    $defaultWeight = 'lbs'; // @todo set from weight type
    if ($e['exercise_group_id'] != $group_id) {
        echo "<h3>{$e['group_type']}</h3>";
        $group_id=$e['exercise_group_id'];
    }
    echo "<div class=\"exercise\">";
    echo "<div class=\"exerciseName\">{$e['exercise_name']}</div>";
    if ($WorkoutInstance) {
        $nextSet = $WorkoutInstance->getNextSetOrdinal($e['exercise_id']);
        for ($i = 0; $i < 4; $i ++) {
            
            $varPrefix = "ex_{$e['exercise_id']}_{$nextSet}";
            $unitsMenu = menu(Sets::$units,"{$varPrefix}_units",$defaultWeight,FALSE, FALSE);
            $setTypesMenu = menu(Sets::$set_types,"{$varPrefix}_type", '', FALSE, FALSE);
            
            echo "<div class=\"exerciseSet\" id=\"$varPrefix\">";
            echo "<div class=\"exerciseSetType\">$setTypesMenu</div>";
            echo "<div class=\"exerciseWeight\"><label>Weight</label> <input class=\"weightInput\" 
            placeholder=\"10\" type=\"number\" step=\"0.1\" name=\"{$varPrefix}_weight\"> $unitsMenu</div>";
            echo "<div class=\"exerciseReps\"><label>Reps</label> <input type=\"number\" class=\"repsInput\" step=\"1\" name=\"{$varPrefix}_reps\"></div>";
            echo "</div>";
            $nextSet++;
        }
    }
    echo "</div>";
}
?>
<style>
div.exerciseSet {
    border: 1px solid green;
    display: inline-block;
    width: 175px !important;
}
div.exerciseSet exerciseSetType {
    display: inline-block;
} 
div.exerciseSet exerciseWeight {
    display: inline;
}
div.exerciseWeight select {
    display: inline !important;
}
div.exerciseSet exerciseUnits {
    display: inline;
}
div.exerciseSet exerciseReps {
    display: inline;
}
input.weightInput, input.repsInput {
    width: 50px;
}
div.exerciseSet label {
    width: 60px;
    display: inline-block;
}

</style>

<?php
Template::endPage();