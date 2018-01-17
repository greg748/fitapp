<?php
require_once ('../../init.php');
global $e;
?>
<div class="exercise">
<div class="exerciseName"><a href="/admin/exercises/edit.php?id=<?=$e['exercise_id']; ?>"><?=$e['exercise_name']; ?></a></div>
<div class="exerciseDesc"><?=$e['description']; ?></div>
<div class="primaryMuscle"><?=$e['primary_muscle_name']; ?></div>
<div class="secondaryMuscles"><?=$e['secondary_muscle_names']; ?></div>
<div class="equipment"><?=$e['equipment']; ?></div>
<div class="weightType"><?=$e['weight_type']; ?></div>
<div class="grip"><?=$e['grip']; ?></div>
<div class="user_position"><?=$e['user_position']; ?></div>
<div class="ability"><?=$e['ability_level']; ?></div>
</div>