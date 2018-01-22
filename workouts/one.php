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
    if ($e['exercise_group_id'] != $group_id) {
        echo "<h3>{$e['group_type']}</h3>";
        $group_id=$e['exercise_group_id'];
    }
    echo "<div class=\"exerciseName\">{$e['exercise_name']}</div>";
    if ($WorkoutInstance) {
        $nextSet = $WorkoutInstance->getNextSetOrdinal($e['exercise_id']);
        $varPrefix = "ex_{$e['exercise_id']}_{$nextSet}";
        $unitsMenu = menu(Sets::$units,"{$varPrefix}_units",'',TRUE, FALSE);
        $setTypesMenu = menu(Sets::$set_types,"{$varPrefix}_type", '', FALSE, TRUE);
        
        echo "<div class=\"exerciseSet\">";
        echo "<div class=\"exerciseSetType\">$setTypesMenu</div>";
        echo "<div class=\"exerciseWeight\">Weight <input type=\"number\" step=\"0.1\" name=\"{$varPrefix}_weight\"></div>";
        echo "<div class=\"exerciseUnits\">$unitsMenu</div>";
        echo "<div class=\"exerciseReps\">Reps <input type=\"number\" step=\"1\" name=\"{$varPrefix}_reps\"></div>";
        echo "</div>";
    }
}
?>


<?php
Template::endPage();