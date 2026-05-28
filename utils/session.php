<?php
declare(strict_types = 1);

class Session {
    private array $messages;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->messages = $_SESSION['messages'] ?? [];
        unset($_SESSION['messages']);
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['id']);
    }

    public function logout(): void {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    public function getId(): ?int {
        return $_SESSION['id'] ?? null;
    }

    public function getName(): ?string {
        return $_SESSION['name'] ?? null;
    }

    public function getUsername(): ?string {
        return $_SESSION['username'] ?? null;
    }

    public function getEmail(): ?string {
        return $_SESSION['email'] ?? null;
    }

    public function getRole(): ?string {
        return $_SESSION['role'] ?? null;
    }

    public function getPlan(): ?string {
        return $_SESSION['plan'] ?? null;
    }

    public function setId(int $id): void {
        $_SESSION['id'] = $id;
    }

    public function setName(string $name): void {
        $_SESSION['name'] = $name;
    }

    public function setUsername(string $username): void {
        $_SESSION['username'] = $username;
    }

    public function setEmail(string $email): void {
        $_SESSION['email'] = $email;
    }

    public function setRole(string $role): void {
        $_SESSION['role'] = $role;
    }

    public function setPlan(string $plan): void {
        $_SESSION['plan'] = $plan;
    }

    public function addMessage(string $type, string $text): void {
        $_SESSION['messages'][] = [
            'type' => $type,
            'text' => $text
        ];
    }

    public function getMessages(): array {
        return $this->messages;
    }
}
?>