<?php 

    class WorkoutClassType {

        private $id;
        private $name;
        private $description;
        private $duration;


        public function __construct(int $id,string $name,string $description,int $duration) { 
         $this->id = $id;
         $this->name = $name;
         $this->description = $description;
         $this->duration = $duration;

        }

        public function getId() { 
            return $this->id;
        }

        public function getName() {
            return $this->name;
        }

        public function getDescription() {
            return $this->description;
        }

        public function getDuration() {
            return $this->duration;
        }

        public static function getWorkoutClassType(PDO $db, int $id): ?WorkoutClassType {

            $stmt = $db->prepare('
            SELECT * 
            FROM ClassType
            WHERE ClassTypeId = ?
            ');

            $stmt->execute([$id]);

            $row = $stmt->fetch();
            
             if ($row === false) {
                return null;
                }


            return new WorkoutClassType(
                $row['id'],
                $row['name'],
                $row['description'],
                $row['duration']
                );

        }        

    }