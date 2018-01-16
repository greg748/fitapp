<?php
require_once '../../init.php';
use Fitapp\classes\WorkoutTypes;
global $w, $rw_id;
$workoutTypesMenu = menu(WorkoutTypes::getWorkoutTypes(), 'workout_type', $w['workout_type'], FALSE);
?>
<form action="/admin/workouts/edit.php" method="post">
<input type="hidden" name="id" value="<?=$w['id'];?>"/>
<input type="hidden" name="rw_id" value="<?=$rw_id;?>"/>
Name: <input type="text" name="name" value="<?=$w['name'];?>"/><br>
Workout Type: <?=$workoutTypesMenu;?><br>
Create Date: <input type="date" name="create_date" value="<?=$w['create_date'];?>"/><br>
Created By: <input type="number" name="created_by" value="<?=$w['created_by'];?>"/><br>
Workout User: <input type="number" name="user_id" value="<?=$w['user_id'];?>"/><br>
Notes: <textarea name="notes" cols="25" rows="3"><?=$w['notes']; ?></textarea><br>
<? if ($w['id']) { ?>
    <button value="save">Save</button>
<? } else { ?>
    <button value="create">Create</button>
    <button value="createAdd">Create and Add Exercises</button>
<? } ?>
</form>