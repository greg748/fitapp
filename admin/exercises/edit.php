<?php
require_once '../../init.php';
use Fitapp\classes\Equipment;
use Fitapp\classes\Exercises;
use Fitapp\classes\ExerciseGroups;
use Fitapp\classes\Muscles;
use Fitapp\classes\Workouts;
use Fitapp\classes\WorkoutTypes;
use Fitapp\classes\WorkoutExercises;
use Fitapp\classes\WeightTypes;
use Fitapp\tools\Template;

$id = $_REQUEST['id'];

if (isset($_REQUEST['workout_id']) && $_REQUEST['workout_id'] > 0) {
    $Workout = Workouts::get($_REQUEST['workout_id']);
    $w = $Workout->getFields();
    $nextGroupOrdinal = $Workout->getNextGroupOrdinal();
    echo $db->errorMsg();
}

if (isset($_POST['saveNewGroup']) || isset($_POST['addToNewGroup'])) {
    $newGroup = ['workout_id'=>$_POST['workout_id'],'group_type'=>$_POST['group_type'], 'group_order'=>$nextGroupOrdinal];
    $Group = ExerciseGroups::create($newGroup);
    if (!$Group) {
        print_pre($_POST);
        die;
    }
    
}
if (isset($_POST['saveToGroup']) || isset($_POST['addToGroup'])) {
    $Group = ExerciseGroups::get($_POST['group_id']);
}
if (isset($_GET['group_id'])) {
    $Group = ExerciseGroups::get($_GET['group_id']);
}

$cache_clear = false;
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_equipment']) && trim($_POST['add_equipment']) !='') {
        $add_id = Equipment::addIfUnique($_POST['add_equipment']);
        $_POST['equipment'][] =$add_id;
        $_POST['equipment'] = array_filter($_POST['equipment']);
        $cache_clear = true;
    }
    if (isset($_POST['add_weight_type']) && trim($_POST['add_weight_type']) != '') {
        $add_id = WeightTypes::addIfUnique($_POST['add_weight_type']);
        $_POST['weight_type'][] =$add_id;
        $_POST['weight_type'] = array_filter($_POST['weight_type']);
        $cache_clear = true;
    }
    
    if (!isset($_POST['existing'])) {
        if ($id) {
            $Exercise = Exercises::get($id);
            $Exercise->setFields($_POST);
            $Exercise->save();
            // error check
            if (!$Exercise->isSaved()) {
                echo "<pre>Save not successful<br>".$Exercise->lastSql()."<pre><br>";
                echo $Exercise->errorMsg();
                print_r($Exercise->problemFields());
                die;
            }
        } else {
            $Exercise = Exercises::create($_POST);
            if (!$Exercise) {
                echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
                die;
            }
            $id = $Exercise->getField('id');
        }
    }
    if ($Group) {
        $group_id = $Group->getField('id');
        $workout_id = ($Workout) ? $Workout->getField('id') : NULL;
        $nextExerciseOrdinal = $Group->getNextExerciseOrdinal();
        $nickname_used = ($_REQUEST['nickname_used']) ?: NULL;
        $rep_pattern = ($_REQUEST['rep_pattern']) ?: [12,10,8];
        $exercise_item = [
            'exercise_id'=>$id,
            'workout_id' => $workout_id,
            'exercise_group_id' => $group_id,
            'exercise_order' => $nextExerciseOrdinal,
            'nickname_used' => $nickname_used,
            'rep_pattern'=> $rep_pattern
          ];
        $WorkoutExercise = WorkoutExercises::create($exercise_item);
    }

} else {
    if (isset($id)) { 
        $e = Exercises::get($id)->getFields();
    } else {
        $e = [];
    }
}

if ($Group) {
    $g = $Group->getFields();
    if (!$Workout) {
        $Workout = Workouts::get($g['workout_id']);
        $w = $Workout->getFields();
    }
} 

$gripsMenu = menu(Exercises::$gripTypes, 'grip', $e['grip'], TRUE);
$abiltitiesMenu = menu(Exercises::$abilities, 'ability_level', $e['ability_level'],FALSE);
$userPositionsMenu = menu(Exercises::$userPositions, 'user_position', $e['user_position'], FALSE);

$muscles = Muscles::getMusclesForMenu();
$primaryMuscles = radio($muscles, 'primary_musc', $e['primary_musc'], TRUE);
$secondaryMuscles = checkbox($muscles, 'secondary_muscs[]', $e['secondary_muscs']);

$equipmentMenu = checkbox(Equipment::getMenu($cache_clear), 'equipment[]', $e['equipment'], TRUE);

$groupTypesMenu = menu(ExerciseGroups::$exercise_group_types, 'group_type', $g['group_type'], FALSE, TRUE, FALSE);
$weightTypesMenu = checkbox(WeightTypes::getMenu($cache_clear), 'weight_type[]', $e['weight_type']);
$workoutTypesMenu = checkbox(WorkoutTypes::getWorkoutTypes(), 'workout_type[]', $e['workout_type']);

if ($Workout) {
    $exercisesMenu = menu(Exercises::getExercisesMenu(['workout_type'=>$w['workout_type']]),'id','', TRUE, FALSE);
}

Template::startPage("Edit Exercise");
if (!$Workout) { ?>notes
<h3>Start new Workout</h3>
<?php require_once '../workouts/workout_input_form.php'; 
} else {
    $exercises = $Workout->getExercises();
    $group_id = '';
    foreach ($exercises as $ex) {
    if ($ex['exercise_group_id'] != $group_id) {
        echo "<h3>{$ex['group_type']} ({$ex['exercise_group_id']}) <a href=\"/admin/exercises/edit.php?group_id={$ex['exercise_group_id']}\">Add to this group</a></h3>";
        $group_id = $ex['exercise_group_id'];
    }
    Exercises::display($ex);
}
} ?>
<form method="post" action="edit.php">
<input type="hidden" name="existing" value="true"/>
<input type="hidden" name="group_id" value="<?=$g['id'];?>"/>
<input type="hidden" name="workout_id" value="<?=$w['id']?>"/>
<?=$exercisesMenu;?>
<?php if ($Group) { ?>
  <button name="addToGroup">Add to Group</button>
<?php } ?>
<button name="addToNewGroup">Add to New Group</button><?=$groupTypesMenu;?>
</form>
<form method="post" action="edit.php">
<input type="hidden" name="id" value="<?=$e['id']?>"/>
<input type="hidden" name="group_id" value="<?=$g['id'];?>"/>
<input type="hidden" name="workout_id" value="<?=$w['id']?>"/>
<table class="display">
<tr>
    <th>Name</th>
    <th>Workout Type</th>
    <th>Ability Level</th>
    <th>Primary Muscle</th>
    <th>Secondary Muscles</th>
    <th>Notes</th>    
</tr>
<tr>
    <td><input type="text" name="name" value="<?=$e['name'];?>"></td>
    <td><?= $workoutTypesMenu; ?></td>
    <td><?= $abiltitiesMenu; ?></td>
    <td><?= $primaryMuscles;?></td>
    <td><?= $secondaryMuscles;?></td>
    <td><textarea name="notes" id="notes" rows="3" cols="25"><?=$e['notes'];?></textarea></td>
</tr>
<tr>
    <th>Description</th>
    <th>Equipment</th>
    <th>User Position</th>
    <th>Grip</th>
    <th>Weight Type</th>
</tr>
<tr>
    <td><textarea name="description" id="description" rows="8" cols="45"><?=$e['description'];?></textarea></td>
    <td><?= $equipmentMenu; ?><br>
        <input type="text" name="add_equipment" value = ''/></td>
    <td><?= $userPositionsMenu; ?></td>
    <td><?= $gripsMenu; ?></td>
    <td><?= $weightTypesMenu; ?>
        <input type="text" name="add_weight_type" value = ''/></td>
    </td>
</tr>
</table>
<button name="save">Save</button> 
<?php //@todo add rep pattern

if ($Group) { ?>
  <button name="saveToGroup">Save to Group</button>
<?php } ?>
<button name="saveNewGroup">Save to New Group</button><?=$groupTypesMenu;?>
</form>
<?php Template::endPage();