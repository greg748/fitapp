<?php
include_once '../../init.php';
use Fitapp\classes\Muscles;
use Fitapp\classes\Exercises;
use Fitapp\tools\Template;

$muscleList = Muscles::getMuscles();
$exercises = Exercises::getExercises(NULL,'ec.name, e.name');
Template::startPage('Exercises');
$classifier = NULL;
foreach ($exercises as $e) {
    if ($e['classifier'] != $classifier) {
        echo "<h2>{$e['classifier']}</h3>";
        $classifier = $e['classifier'];
    }
    Exercises::display($e);
}
Template::endPage();