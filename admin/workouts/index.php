<?php
include_once '../../init.php';

use Fitapp\classes\Workouts;
use Fitapp\classes\WorkoutTypes;
use Fitapp\tools\Template;

$workouts = Workouts::getAllWorkouts();

$workoutTypesMenu = menu(WorkoutTypes::getWorkoutTypes(), 'workout_type', $w['workout_type'], FALSE);
Template::startPage("Workouts");
?>
<h3>Existing Workouts</h3>
<table class="display">
    <tr>
        <th>Name</th>
        <th>Type</th>
        <th>Created</th>
        <th>User</th>
        <th>Created By</th>
        <th>Notes</th>
        <th>Actions</th>
    </tr>
    <tr>
        <td><form action="/admin/workouts/edit.php" method="post"><input type="text" name="name" value="<?=$w['name'];?>"/></td>
        <td><?=$workoutTypesMenu;?></td>
        <td><input type="date" name="create_date" value="<?=$w['create_date'];?>"/></td>
        <td><input type="number" name="user_id" value="<?=$w['user_id'];?>"/></td>
        <td><input type="number" name="created_by" value="<?=$w['created_by'];?>"/></td>
        <td><textarea name="notes" cols="25" rows="3"><?=$w['notes']; ?></textarea></td>
        <td><? if ($w['id']) { ?>
            <button name="save" value="save">Save</button>
        <? } else { ?>
            <button name="create" value="create">Create</button>
            <button name="createAdd" value="createAdd">Create and Add Exercises</button>
        <? } ?></form></td>

    </tr>
<?php
foreach ($workouts as $wo) {
    echo "<tr>";
    echo "<td><a href=\"/admin/workouts/edit.php?id={$wo['id']}\">{$wo['name']}</a>";
    echo "<td>{$wo['workout_type_name']}</td>";
    echo "<td>{$wo['create_date']}</td>";
    echo "<td>{$wo['firstname']} {$wo['laststname']} ({$wo['username']})</td>";
    echo "<td>{$wo['cb_firstname']} {$wo['cb_laststname']} ({$wo['cb_username']})</td>";
    echo "<td>{$wo['notes']}</td>";
    echo "<td><a href=\"/admin/exercises/edit.php?workout_id={$wo['id']}\">Add Exercises</a></td>";
    echo "</tr>\n";
}
?>
</table>
<? 
Template::endPage();


