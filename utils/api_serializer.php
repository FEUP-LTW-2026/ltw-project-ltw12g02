<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/users.class.php');
require_once(__DIR__ . '/../database/trainers.class.php');

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