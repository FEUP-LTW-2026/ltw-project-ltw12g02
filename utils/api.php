<?php
declare(strict_types = 1);

function sendJson(mixed $data, int $status_code = 200) : void{

    http_response_code($status_code);

    header('Content-type: application/json; charset=utf-8');

    echo json_encode($data, JSON_PRETTY_PRINT);

    exit;


}

function getJsonInput(): array {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!is_array($input)) {
        return [];
    }

    return $input;
}