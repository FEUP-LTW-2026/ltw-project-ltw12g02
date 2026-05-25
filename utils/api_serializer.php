<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/users.class.php');

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