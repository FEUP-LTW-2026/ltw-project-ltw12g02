<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');
require_once(__DIR__ . '/../database/workoutclass.class.php');
require_once(__DIR__ . '/../database/workoutclasstype.class.php');
require_once(__DIR__ . '/../database/equipment.class.php');

function userToJson(Users $user): array {
    return [
        'id' => $user->getUserId(),
        'name' => $user->getName(),
        'username' => $user->getUserName(),
        'email' => $user->getEmail(),
        'role' => $user->getRole(),
        'profileImage' => $user->getProfileImage()
    ];
}

function usersToJson(array $users): array {
    $result = [];

    foreach ($users as $user) {
        $result[] = userToJson($user);
    }

    return $result;
}

function trainerToJson(Trainers $trainer, PDO $db): array {
    $user = Users::getUser($db, $trainer->getUserId());

    return [
        'id' => $trainer->getTrainerId(),
        'userId' => $trainer->getUserId(),
        'bio' => $trainer->getBio(),
        'specializations' => $trainer->getSpecializations(),
        'certifications' => $trainer->getCertifications(),
        'user' => $user !== null ? userToJson($user) : null
    ];
}

function trainersToJson(array $trainers, PDO $db): array {
    $result = [];

    foreach ($trainers as $trainer) {
        $result[] = trainerToJson($trainer, $db);
    }

    return $result;
}

function workoutClassTypeToJson(WorkoutClassType $type): array {
    return [
        'id' => $type->getId(),
        'name' => $type->getName(),
        'description' => $type->getDescription(),
        'duration' => $type->getDuration()
    ];
}

function workoutClassTypesToJson(array $types): array {
    $result = [];

    foreach ($types as $type) {
        $result[] = workoutClassTypeToJson($type);
    }

    return $result;
}

function workoutClassToJson(WorkoutClass $class, PDO $db): array {
    $classType = WorkoutClassType::getWorkoutClassType($db, $class->getClassTypeId());
    $trainer = Trainers::getTrainer($db, $class->getTrainerId());

    $enrollmentCount = WorkoutClass::getEnrollmentCount($db, $class->getId());

    return [
        'id' => $class->getId(),
        'trainerId' => $class->getTrainerId(),
        'classTypeId' => $class->getClassTypeId(),
        'classDateTime' => $class->getClassDateTime(),
        'capacity' => $class->getCapacity(),
        'enrollmentCount' => $enrollmentCount,
        'availableSlots' => max(0, $class->getCapacity() - $enrollmentCount),
        'isFull' => WorkoutClass::isFull($db, $class->getId(), $class->getCapacity()),
        'trainerName' => $class->getTrainerName($db),
        'trainer' => $trainer !== null ? trainerToJson($trainer, $db) : null,
        'classType' => $classType !== null ? workoutClassTypeToJson($classType) : null
    ];
}

function workoutClassesToJson(array $classes, PDO $db): array {
    $result = [];

    foreach ($classes as $class) {
        $result[] = workoutClassToJson($class, $db);
    }

    return $result;
}

function equipmentToJson(Equipment $equipment): array {
    return [
        'id' => $equipment->getId(),
        'name' => $equipment->getName(),
        'type' => $equipment->getType(),
        'quantity' => $equipment->getQuantity(),
        'status' => $equipment->getStatus()
    ];
}

function equipmentListToJson(array $equipmentList): array {
    $result = [];

    foreach ($equipmentList as $equipment) {
        $result[] = equipmentToJson($equipment);
    }

    return $result;
}
?>