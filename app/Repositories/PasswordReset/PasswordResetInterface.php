<?php

namespace App\Repositories\PasswordReset;

interface PasswordResetInterface
{
    public function create($email, $token);

    public function getPasswordResetByEmail($email);

    public function DeleteAllResetPasswordByEmail($email);

    public function getPasswordResetByToken($token);
}
