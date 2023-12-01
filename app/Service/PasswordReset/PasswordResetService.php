<?php

namespace App\Service\PasswordReset;

use App\Repositories\PasswordReset\PasswordResetInterface;

class PasswordResetService
{
    public $passwordReset;

    public function __construct(PasswordResetInterface $passwordReset)
    {
        $this->passwordReset = $passwordReset;
    }

    public function create($email, $token)
    {
        return $this->passwordReset->create($email, $token);
    }

    public function deleteAllToken($email)
    {
        return $this->passwordReset->DeleteAllResetPasswordByEmail($email);
    }

    public function getPasswordReset($email)
    {
        return $this->passwordReset->getPasswordResetByEmail($email);
    }

    public function getPasswordResetByToken($token)
    {
        return $this->passwordReset->getPasswordResetByToken($token);
    }
}
