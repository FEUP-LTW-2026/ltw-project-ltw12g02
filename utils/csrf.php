<?php
declare(strict_types = 1);

function ensureSessionStarted(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function generateCSRFToken(): string {
    ensureSessionStarted();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function sendCSRF(): void {
    $token = generateCSRFToken();
    ?>
    <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
    <?php
}

function checkCSRF(string $token): bool {
    ensureSessionStarted();

    if (empty($_SESSION['csrf_token'])) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

function evaluateCSRF(string $token): void {
    if (!checkCSRF($token)) {
        header('Location: ../pages/index.php');
        exit;
    }
}