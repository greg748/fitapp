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
if (isset($_REQUEST['wi_id'])) {
    $WorkoutInstance = WorkoutInstances::get($_REQUEST['wi_id']);
    $wi = $WorkoutInstance->getFields();
    $wi_id = $wi['id'];
    $_SESSION['workout_instance'] = $wi_id;
    $Workout = Workouts::get($wi['workout_id']);
    $w = $Workout->getFields();
} elseif (isset($_REQUEST['createInstance'])) {
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
    foreach ($_POST as $key=>$val) {
        list($ex,$group_id,$ex_id,$set_no,$field) = explode('_',$key);
        if (isset($field) && $val != '') {
            $sets[$group_id][$ex_id][$set_no][$field] = $val;
        }
    }
    foreach ($sets as $group_id=>$exercises) {
        foreach ($exercises as $ex_id=>$setlist) { 
            foreach ($setlist as $set_no =>$set) {
                if (isset($set['reps']) || isset($set['set_id'])) {
                    $set['exercise_id'] = $ex_id;
                    $set['group_id'] = $group_id;
                    $set['workout_instance_id'] = $wi_id;
                    $set['set_order'] = $set_no;
                    $result = Sets::saveSet($set, TRUE);
                }
            }
        }
    }
    //die;
}

Template::startPage('Workout Instance');
if (!$WorkoutInstance) {
    echo "<a href=\"?createInstance=true\">Create Instance</a>";
} else { 
    $inputs = $WorkoutInstance->getSetsForInputs();
    print_pre($inputs);
    ?>
<form method="POST" action="one.php">
<input type="hidden" name="wi_id" value="<?=$wi_id;?>"/>
<?php

}
$group_id = NULL;
foreach ($exercises as $e) {
    
    if ($e['exercise_group_id'] != $group_id) {
        echo "<h3>{$e['group_type']}</h3>";
        $group_id=$e['exercise_group_id'];
    }
    echo "<div class=\"exercise\">";
    echo "<div class=\"exerciseName\">{$e['exercise_name']}</div>";
    if ($WorkoutInstance) {
        $nextSet = $WorkoutInstance->getNextSetOrdinal($e['exercise_id']);
        for ($i = 1; $i <= 4; $i ++) {
            $varPrefix = "ex_{$group_id}_{$e['exercise_id']}_{$i}";
            $defaultWeight = ($inputs["{$varPrefix}_units"]) ?: 'lbs'; // @todo set from weight type
            
            $unitsMenu = menu(Sets::$units,"{$varPrefix}_units",$defaultWeight,FALSE, FALSE);
            $setTypesMenu = menu(Sets::$set_types,"{$varPrefix}_type", $inputs["{$varPrefix}_type"], FALSE, FALSE);
            if (isset($inputs["{$varPrefix}_id"])) {
                echo "<input type=\"hidden\" name=\"{$varPrefix}_id\" value={$inputs["{$varPrefix}_id"]}\"/>"; 
            }
            echo "<div class=\"exerciseSet\" id=\"$varPrefix\">";
            echo "<div class=\"exerciseSetType\">$setTypesMenu</div>";
            echo "<div class=\"exerciseWeight\"><label>Weight</label> <input class=\"weightInput\" 
            placeholder=\"10\" type=\"number\" value=\"{$inputs["{$varPrefix}_weight"]}\" step=\"0.1\" name=\"{$varPrefix}_weight\"> $unitsMenu</div>";
            echo "<div class=\"exerciseReps\"><label>Reps</label> <input type=\"number\" class=\"repsInput\" step=\"1\" value=\"{$inputs["{$varPrefix}_reps"]}\" name=\"{$varPrefix}_reps\"></div>";
            echo "</div>";
            // $nextSet++;
        }
    }
    echo "</div>";
    echo "<button>Save</button>";
}
?>
</form>
<style>
div.exerciseSet {
    border: 1px solid green;
    display: inline-block;
    width: 175px !important;
}
div.exerciseSet .exerciseSetType {
    display: inline-block;
} 
div.exerciseSet .exerciseWeight {
    display: inline;
    white-space: nowrap;
}
div.exerciseWeight select {
    display: inline !important;
}
div.exerciseSet .exerciseUnits {
    display: inline;
}
div.exerciseSet .exerciseReps {
    display: inline;
}
input.weightInput, input.repsInput {
    width: 50px;
}
div.exerciseSet label {
    width: 60px;
    display: inline-block;
    font-size: 80%;
}

</style>

<?php
Template::endPage();