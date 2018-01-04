<?php
include_once '../../init.php';
use Fitapp\classes\Exercises;
use Fitapp\classes\Equipment;
use Fitapp\classes\Muscles;
use Fitapp\classes\WorkoutTypes;
use Fitapp\classes\WeightTypes;

$e = [];

$gripsMenu = menu(Exercises::$gripTypes,'grip',$e['grip'],FALSE);
$abiltitesMenu = menu(Exercises::$abilities,'ability_level',$e['ability'],FALSE);
$userPositionsMenu = menu(Exercises::$userPositions,'user_position',$e['user_position'], FALSE);

$muscles = Muscles::getMusclesForMenu();
$primaryMuscles = menu($muscles,'primary_musc', $e['primary_musc'], TRUE);
$secondaryMuscles = checkbox($muscles,'secondary_musc[]',$e['secondary_musc']);

$equipment = Equipment::getEquipmentMenu();
$equipmentMenu = menu($equipmentMenu, 'equipment[]', $e['equipment'], TRUE);

$workoutTypesMenu = checkbox(WorkoutTypes::getWorkoutTypes(), 'workout_type[]', $e['workout_type']);
$weightTypesMenu = checkbox(WeightTypes::getWeightTypesMenu(), 'weight_type[]', $e['weight_type']);
?>
<style>
table.display { border: 1px solid #999;}
table.display th { text-align: left: vertical-align: top; 
    font-weight: bold; font-size: 12px; background-color: #ccc}
table.display td { text-align: left; vertical-align: top;}
span.checkbox, span.radio { white-space: nowrap; }
</style>

<table class="display">
<tr>
    <th>Name</th>
    <th>Primary Muscle</th>
    <th>Secondary Muscles</th>
    <th>Description</th>
    <th>Ability Level</th>
    <th>Equipment</th>
    <th>User Position</th>
    <th>Workout Type</th>
    <th>Grip</th>
    <th>Weight Type</th>
    <th>Notes</th>  
</tr>
<tr>
    <td><input type="text" name="name" value="<?=$e['name'];?>"></td>
    <td><?=$primaryMuscles;?></td>
    <td><?=$secondaryMuscles;?></td>
    <td><input type="text" name="description" value="<?=$e['description'];?>"></td>
    <td>Ab</td>
    <td><?= $equipmentMenu; ?><br>
        <input type="text" name="add_equipment" value = ''/></td>
    <td><?= $userPositionsMenu; ?></td>
    <td><?= $workoutTypesMenu; ?></td>
    <td><?= $gripsMenu; ?></td>
    <td><?= $weightTypesMenu; ?>
        <input type="text" name="add_weight_type" value = ''/></td>
    </td>
    <td><textarea name="notes" id="notes" rows="3" cols="25"><?=$e['notes'];?></textarea></td>
</tr>
</table>
