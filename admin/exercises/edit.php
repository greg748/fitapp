<?php
require_once '../../init.php';
use Fitapp\classes\Equipment;
use Fitapp\classes\Exercises;
use Fitapp\classes\ExerciseGroups;
use Fitapp\classes\Muscles;
use Fitapp\classes\Workouts;
use Fitapp\classes\WorkoutTypes;
use Fitapp\classes\WorkoutItems;
use Fitapp\classes\WeightTypes;
use Fitapp\tools\Template;

$id = $_REQUEST['id'];

if (isset($_REQUEST['workout_id'])) {
    $Workout = Workouts::get($_REQUEST['workout_id']);
    $w = $Workout->getFields();
}

if (isset($_POST['saveNewGroup'])) {
    $newGroup = ['group_type'=>$_POST['group_type']];
    $Group = ExerciseGroups::create($newGroup);
    if (!$Group) {
        print_pre($_POST);
        die;
    }
    $Group->addToWorkout($w['id']);
}
if (isset($_POST['saveToGroup'])) {
    $Group = ExerciseGroups::get($_POST['group_id']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_equipment']) && trim($_POST['add_equipment']) !='') {
        $add_id = Equipment::addIfUnique($_POST['add_equipment']);
        echo "New equipment id = $add_id";
        array_push($_POST['equipment'], $add_id);
        $_POST['equipment'] = array_filter($_POST['equipment']);
    }
    if (isset($_POST['add_weight_type']) && trim($_POST['add_weight_type']) != '') {
        $add_id = WeightTypes::addIfUnique($_POST['add_weight_type']);
        echo "New weight type id = $add_id";
        array_push($_POST['weight_type'], $add_id);
        $_POST['weight_type'] = array_filter($_POST['weight_type']);
    }
  
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
        if (isset($_POST['saveNewGroup']) && $Group) {
            $Group->addExercise($id);
        }
    } else {
        $Exercise = Exercises::create($_POST);
        if (!$Exercise) {
            echo "<pre>Error! ".$db->lastSql(). "\n". $db->errorMsg(). "</pre>";
            die;
        }
        $id = $Exercise->getField('id');
        if ($Group) {
            $Group->addExercise($id);
        }
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
} 

$gripsMenu = menu(Exercises::$gripTypes, 'grip', $e['grip'], TRUE);
$abiltitiesMenu = menu(Exercises::$abilities, 'ability_level', $e['ability_level'],FALSE);
$userPositionsMenu = menu(Exercises::$userPositions, 'user_position', $e['user_position'], FALSE);

$muscles = Muscles::getMusclesForMenu();
$primaryMuscles = radio($muscles, 'primary_musc', $e['primary_musc'], TRUE);
$secondaryMuscles = checkbox($muscles, 'secondary_muscs[]', $e['secondary_muscs']);

$equipmentMenu = checkbox(Equipment::getEquipmentMenu(), 'equipment[]', $e['equipment'], TRUE);

$groupTypesMenu = menu(ExerciseGroups::$ex_group_types, 'group_type', $g['group_type'], FALSE, TRUE, FALSE);
$workoutTypesMenu = checkbox(WorkoutTypes::getWorkoutTypes(), 'workout_type[]', $e['workout_type']);
$weightTypesMenu = checkbox(WeightTypes::getWeightTypesMenu(), 'weight_type[]', $e['weight_type']);
Template::startPage("Edit Exercise");

if (!$Workout) { ?>
<h3>Start new Workout</h3>
<? require_once '/admin/workouts/workout_input_form.php'; 
} else {
    print_pre($w);
} ?>
<? if ($Group) {
    echo "Group type {$g['group_type']}";
    foreach ($g['exercise_ids'] as $ex_id) {
        Exercises::display($ex_id);
    }
} ?>

<form method="post" action="edit.php">
<input type="hidden" name="id" value="<?=$e['id']?>"/>
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
    <td><textarea name="notes" id="description" rows="3" cols="25"><?=$e['description'];?></textarea></td>
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
<? if ($Group) { ?>
  <input type="hidden" name="group_id" value="<?=$g['id'];?>"/>
  <button name="saveToGroup">Save to Group</button>
<? } ?>
<button name="saveNewGroup">Save to New Group</button><?=$groupTypesMenu;?>
</form>
<? Template::endPage();