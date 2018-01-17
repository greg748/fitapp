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
        echo "<h3>{$e['group_type']} ({$e['exercise_group_id']})</h3>";
        $group_id = $e['exercise_group_id'];
    }
    Exercises::display($e['id']);
    //print_pre($e);
}
$style='.muscle-groups svg path { fill: #ffffff; stroke-width: 1px; stroke-color: #666666;} ';
$scoreTable =  "<div class=\"scoreMuscle header\">Muscle</div>";
$scoreTable .= "<div class=\"scorePrimary header\">Primary</div>";
$scoreTable .= "<div class=\"scoreSecondary Header\">Secondary</div>";
$scoreTable .= "<div class=\"scoreColor Header\">Color</div>";
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
    if ($s['total_score'] > 0) { 
        $scoreTable .= "<div class=\"scoreMuscle\">".ucWords($s['muscle_name'])."</div>";
        $scoreTable .= "<div class=\"scorePrimary\">".$s['primary_score']."</div>";
        $scoreTable .= "<div class=\"scoreSecondary\">".$s['secondary_score']."</div>";
        $scoreTable .= "<div class=\"scoreColor\">".$s['css']."</div>";
        $scoreTable .= "\n";
       
    
    }
    $style .= "\n.muscle-groups svg #{$s['group_name']} path {
        opacity: .8;
        fill: #{$s['css']} !important;
      }";
}
?>
<div id="scoreBlock">
    <div class="scoreTable"><?=$scoreTable; ?></div>
    <div class="scoreDiagram">
    <svg class="muscle-groups" height="400" width="400" xmlns="http://www.w3.org/2000/svg">
      <?php include_once '../../img/body_diagram.svg'; ?>
    </svg>
    </div>


</div>



<?php
// $style = ".muscle-groups svg #Abs path { fill: #999900 !important; }";
echo "<style>
div.scoreTable { display: inline-block; width: 350px; vertical-align: top; border: 1px solid #ccc}
div.scoreDiagram { display: inline-block; width: 400px; border: 1px solid #ccc;}
div.scoreMuscle { clear: left; display: inline-block; width: 125px; }
div.scorePrimary, div.scoreSecondary, div.scoreColor { display: inline-block; width: 75px} 
div.header { font-weight: bold; }



$style</style>"; 
Template::endPage();