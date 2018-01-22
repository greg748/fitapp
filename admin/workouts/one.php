<?php
include_once '../../init.php';
use Fitapp\classes\Exercises;
use Fitapp\classes\Workouts;
use Fitapp\tools\Template;


$Workout = Workouts::get($_REQUEST['id']);
$exercises = $Workout->getExercises();
$scores = $Workout->getMuscleScores();
Template::startPage($Workout->getField('name') . ': Exercises');
$group_id = '';
foreach ($exercises as $e) {
    if ($e['exercise_group_id'] != $group_id) {
        echo "<h3>{$e['exercise_group_order']} {$e['group_type']}"; 
        echo "<a href=\"/admin/exercises/edit.php?group_id={$e['exercise_group_id']}\">Add to this group</a> ";
        echo "<a href=\"/admin/groups/edit.php?id={$e['exercise_group_id']}\">Edit group</a> ";
        echo "<a href=\"/admin/groups/edit.php?id={$e['exercise_group_id']}&delete=delete\">Delete from Workout</a></h3>";
        $group_id = $e['exercise_group_id'];
    }
    Exercises::display($e);
}

Workouts::getScoreBlock($scores);

Template::endPage();