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
<svg class="muscle-groups" height="400" width="800" xmlns="http://www.w3.org/2000/svg">
      <?php include_once '../../img/body_diagram.svg'; ?>
</svg>
<?php
$group_id = '';
foreach ($exercises as $e) {
    if ($e['ex_group_id'] != $group_id) {
        echo "<h3>{$e['group_type']}</h3>";
        $group_id= $e['ex_group_id'];
    }
    Exercises::display($e['id']);
    print_pre($e);
}
$style='.muscle-groups svg path { fill: #ffffff; stroke-width: 1px; stroke-color: #666666;} ';
foreach ($scores as $s) {
    // $color2= 255;
    $s['total_score'] = $s['primary_score'] + $s['secondary_score'];
    if ($s['total_score'] == 0.0) {
        $s['css'] = 'ffffff';
    } else {
        $color = ($s['primary_score'] > 0.0) ? 255-ceil(35*($s['primary_score'])) : 0;
        $color2= ($s['secondary_score'] > 0.0) ? 255-ceil(65*($s['secondary_score'])) :255;
        $s['css'] = sprintf("00%02x%02x", $color, $color2);
    }
    $s['group_name'] = str_replace(' ','-',ucWords($s['muscle_name']));
    if ($s['total_score'] > 0) { print_pre($s); }
    $style .= "\n.muscle-groups svg #{$s['group_name']} path {
        opacity: .8;
        fill: #{$s['css']} !important;
      }";
}
// $style = ".muscle-groups svg #Abs path { fill: #999900 !important; }";
echo "<style>$style</style>"; 
Template::endPage();