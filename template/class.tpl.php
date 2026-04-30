<?php 
require_once (__DIR__ . '/../database/workoutclasstype.class.php');
function drawClassHeader(WorkoutClassType $workoutClassType){



?><section class="image-bg">
            <div id="class-header">
                <h1><?= $workoutClassType->getName()?></h1>
                <img src="https://picsum.photos/600/300" alt="illustrative" width="600" height="300">
                <a href="#available-classes" class="btn light">Schedule class</a>
            </div>
        </section>


 <?php }