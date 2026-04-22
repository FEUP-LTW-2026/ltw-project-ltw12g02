
<?php
class Workoutclass {
    private $time;
    private $trainer;
    private $room;
    private $spots;

    public function __construct($time, $trainer, $room, $spots) {
        $this->time = $time;
        $this->trainer = $trainer;
        $this->room = $room;
        $this->spots = $spots;
    }

    public function getTime() {
        return $this->time;
    }

    public function getTrainer() {
        return $this->trainer;
    }

    public function getRoom() {
        return $this->room;
    }

    public function getSpots() {
        return $this->spots;
    }


    public function isAvailable() {
        return $this->spots > 0;
    }
    

}