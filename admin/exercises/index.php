<?php
include_once '../../init.php';
use Fitapp\classes\Muscles;
use Fitapp\classes\Exercises;
use Fitapp\tools\Template;
echo "Here";

$muscleList = Muscles::getMuscles();
$exercises = Exercises::getExercises();
Template::startPage('Exercises');
foreach ($exercises as $e) {
    Exercises::display($e);
}
Template::endPage();