<?php
require_once ('../../init.php');
global $e;
?>
<div class="exercise">
<div class="exerciseName"><a href="edit.php?id=<?=$e['ex_id']; ?>"><?=$e['ex_name']; ?></a></div>
<div class="exerciseDesc"><?=$e['description']; ?></div>
<div class="primaryMuscle"><?=$e['primary_musc_name']; ?></div>
<div class="secondaryMuscles"><?=$e['secondary_musc_names']; ?></div>
<div class="equipment"><?=$e['equipment']; ?></div>
<div class="weightType"><?=$e['weight_type']; ?></div>
<div class="grip"><?=$e['grip']; ?></div>
<div class="user_position"><?=$e['user_position']; ?></div>
<div class="ability"><?=$e['ability_level']; ?></div>
</div>