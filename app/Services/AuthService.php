<?php

namespace App\Services;

class AuthService
{
    public function isAuthenticated(): bool
    {
        return isset($_SESSION['user']);
    }
}
