

<?php 
require_once(__DIR__ . "/../database/workoutclass.class.php");

function drawWorkoutClass(Workoutclass $workoutClass) { ?>

 <article class= <?php if ($workoutClass->isAvailable()) {
    echo "class-session";
 } else {
    echo "class-session-full";
 } ?> >
      <h3>Saturday</h3>
      <p><strong>Time:</strong> <?= $workoutClass->getTime()?></p>
      <p><strong>Trainer:</strong> <?= $workoutClass->getTrainer()?></p>
      <p><strong>Room:</strong> <?= $workoutClass->getRoom()?></p>
      <p><strong>Available spots:</strong> <?= $workoutClass->getSpots()?></p>
      <a class="btn small light" href="booking.html">Book class</a>
    </article>

<?php } 

function drawWorkoutClasses( array $workoutClasses) {

   foreach ($workoutClasses as $workoutClass) { 

      drawWorkoutClass($workoutClass);

    }

 } 


