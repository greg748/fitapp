<?php
namespace Fitapp\traits;

use Fitapp\classes\AppConfig;
use Fitapp\classes\Exercises;
use Fitapp\tools\ColorFunctions;

trait ScorableTrait {
    
    /**
    * Scores the workout/regimen based on muscle groups used
    *
    * @return Array Muscles and their scores
    */
    public function getMuscleScores() {
        $id = $this->getField('id');
        $sql ="SELECT name as muscle_name, 0 as primary_score, 0 as secondary_score from muscles";
        $results = $this->db->Execute($sql);
        $scores = [];
        foreach ($results as $r) {
            $scores[$r['muscle_name']] = $r;
        }
        if ($this instanceof \Fitapp\classes\Regimens) {
            $sql = "SELECT rw.workout_id
                FROM regimen_workouts rw
                WHERE regimen_id=$id
                AND rw.status='a'
                AND rw.add_date IS NOT NULL 
                AND NOW()::DATE between rw.add_date AND coalesce(rw.remove_date, NOW()::DATE)";
            $results = $this->db->Execute($sql);
            $worout_ids = [];
            foreach ($results as $r) {
                $workout_ids[] = $r['workout_id'];
            }
            $workout_id_list = implode(',',$workout_ids);
        } else {
            $workout_id_list = $id;
        }

        // @todo give more score to single/alt exercises
        // @todo give more score to bosu/physio exercises... but they may already be higher level
        $sql = "SELECT muscle_name, e.ability_level, 
            SUM(primary_score) AS primary_score, 
            SUM(secondary_score) as secondary_score
            FROM exercise_muscles em
            JOIN workout_exercises we ON we.exercise_id=em.exercise_id
            JOIN exercises e on e.id=we.exercise_id
            WHERE we.workout_id in ($workout_id_list)
            GROUP BY muscle_name, e.ability_level
            ORDER BY 2 DESC";
            $results = $this->db->Execute($sql);
            echo $this->db->errorMsg();
        $abilityMults = Exercises::$abilityMultipliers;
        foreach ($results as $r) {
            $scores[$r['muscle_name']]['primary_score'] += $r['primary_score']*$abilityMults[$r['ability_level']];
            $scores[$r['muscle_name']]['secondary_score'] += $r['secondary_score']*$abilityMults[$r['ability_level']];
        }
        return $scores;
      }


  
    
    public static function getScoreBlock($scores = [], $wrapper_id = 'wrapper123') {
        $blue_low = array(255, 255, 255); //'#ddddff';
        $blue_low = [173, 204, 255];
        $blue_high = [0, 0, 255]; //'#020277';
        $red_low = array(255, 255, 255);
        $red_high = array(225,0,0);
        
        $primary_segments = 12;
        $secondary_segments = 20;
        $blendMult = $secondary_segments/$primary_segments*2;
        $primary_vals = ColorFunctions::graduateRGB($blue_low,$blue_high, $primary_segments);
        $secondary_vals = ColorFunctions::graduateRGB($red_low,$red_high, $secondary_segments);
        
        $style="div#{$wrapper_id} .muscle-groups svg path { fill: #ffffff; stroke-width: 1px; stroke-color: #666666;} ";
        $scoreTable =  "<div class=\"scoreMuscle header\">Muscle</div>";
        $scoreTable .= "<div class=\"scorePrimary header\">Primary</div>";
        $scoreTable .= "<div class=\"scoreSecondary Header\">Secondary</div>";
        //$scoreTable .= "<div class=\"scoreColor Header\">Color</div>";
        $primaryTotal = 0.0;
        
        foreach ($scores as $s) {
            // $color2= 255;
            $s['total_score'] = $s['primary_score'] + $s['secondary_score'];
            $s['css'] = 'ffffff';
            $s['group_name'] = str_replace(' ','-',ucWords($s['muscle_name']));
            if ($s['total_score'] > 0) {
                $secondary_segment = min($secondary_segments-1, ceil($s['secondary_score']*10));
                $primary_segment = min($primary_segments-1, ceil($s['primary_score']));
                if ($s['primary_score'] == 0) {
                    $s['css'] = ColorFunctions::RGB2Hex($secondary_vals[$secondary_segment]);
                } elseif ($s['secondary_score'] == 0) {
                    $s['css'] = ColorFunctions::RGB2Hex($primary_vals[$primary_segment]);
                } else {
                    $s['css'] = ColorFunctions::blendRGB($primary_vals[$primary_segment], $secondary_vals[$secondary_segment], $blendMult);
                }
                $scoreTable .= "<div class=\"scoreMuscle\">".ucWords($s['muscle_name'])."</div>";
                $scoreTable .= "<div class=\"scorePrimary\">".$s['primary_score']."</div>";
                $scoreTable .= "<div class=\"scoreSecondary\">".$s['secondary_score']."</div>";
                //$scoreTable .= "<div class=\"scoreColor\">".$s['css']."</div>";
                $scoreTable .= "\n";
                $primaryTotal += $s['primary_score'];
            }
            $style .= "\ndiv#{$wrapper_id} .muscle-groups svg #{$s['group_name']} path {
                opacity: .8;
                fill: #{$s['css']} !important;
            }";
        }
        if ($primaryTotal > 0) { 
            $scoreTable .= "<div class=\"scoreMuscle\">Total</div>";
            $scoreTable .= "<div class=\"scorePrimary\">$primaryTotal</div>";
            $scoreTable .= "\n";
        }
        $app_dir = AppConfig::get()->getAppDir();
        $body_diagram = $app_dir . '/img/body_diagram.svg';
        $svg = file_get_contents($body_diagram);
        echo "<h3>Scores</h3>
        <div class=\"scoreBlock\">
            <div class=\"scoreTable\">$scoreTable</div>
            <div id=\"$wrapper_id\" class=\"scoreDiagram\">
            <svg class=\"muscle-groups\" height=\"400\" width=\"400\" xmlns=\"http://www.w3.org/2000/svg\">
            $svg
            </svg>
            </div>
        </div>
        <style>$style<style>
        ";
    }

}