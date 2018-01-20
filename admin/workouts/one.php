<?php
include_once '../../init.php';
use Fitapp\classes\Exercises;
use Fitapp\classes\Workouts;
use Fitapp\tools\Template;


$Workout = Workouts::get($_REQUEST['id']);
$exercises = $Workout->getExercises();
$scores = $Workout->getMuscleScores();
Template::startPage('Exercises for this Workout');
$group_id = '';
foreach ($exercises as $e) {
    if ($e['exercise_group_id'] != $group_id) {
        echo "<h3>{$e['group_type']} ({$e['exercise_group_id']}) <a href=\"/admin/exercises/edit.php?group_id={$e['exercise_group_id']}\">Add to this group</a></h3>";
        $group_id = $e['exercise_group_id'];
    }
    Exercises::display($e);
}

Workouts::getScoreBlock($scores);

Template::endPage();