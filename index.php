<?php

require_once __DIR__ . '/template/common.tpl.php';
require_once __DIR__ . '/template/workoutclass.tpl.php';
require_once __DIR__ . '/database/workoutclass.class.php';
?>

<?= generateHead() ?>
<body>
    <?= generateHeader() ?>

    <section class="class-row">
        <?php
        $workoutclass = new Workoutclass('10:00', 'Nuno Lima', 7, 1);
        $workoutclass2 = new Workoutclass('10:00', 'Nuno Lima', 7, 7);

        drawWorkoutClasses([$workoutclass, $workoutclass2, $workoutclass,$workoutclass2]);
       
        ?>
    </section>

    <?= generateFooter() ?>
</body>
</html>