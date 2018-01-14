<?php
include_once '../../init.php';
use Fitapp\classes\Exercises;
use Fitapp\classes\Workouts;
use Fitapp\tools\Template;


$Workout = Workouts::get($_REQUEST['id']);
$exercises = $Workout->getExercises();
$scores = $Workout->getMuscleScores();
Template::startPage('Exercises for this Workout');
?>
<svg height="400" width="800" xmlns="http://www.w3.org/2000/svg">
      <?php include_once '../../img/body_diagram.svg'; ?>
</svg>
<?php
foreach ($exercises as $e) {
    Exercises::display($e['id']);
    print_pre($e);
}
foreach ($scores as $s) {
    $color = dechex(ceil(255 - 17*($s['muscle_score'])));
    $s['css'] = "$color$color$color";
    print_pre($s);
}
$groups; 
Template::endPage();