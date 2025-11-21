<?php

class Auth
{
    protected ?array $user = null;

    public function __construct()
    {
        $this->user = $_SESSION['user'] ?? null;
    }

    public function attempt(string $username, string $password): bool
    {
        $userModel = new UserModel();
        $user = $userModel->getByUsername($username);

        if (!$user || !$user['status']) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'employee_id' => $user['employee_id'],
        ];

        $this->user = $_SESSION['user'];

        return true;
    }

    public function logout(): void
    {
        unset($_SESSION['user']);
        $this->user = null;
    }

    public function check(): bool
    {
        return !empty($this->user);
    }

    public function user(): ?array
    {
        return $this->user;
    }

    /**
     * @param array|string $roles
     */
    public function hasRole($roles): bool
    {
        if (!$this->check()) {
            return false;
        }

        $roles = (array) $roles;
        return in_array($this->user['role'], $roles, true);
    }
}

