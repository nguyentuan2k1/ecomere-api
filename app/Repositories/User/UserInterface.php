<?php

namespace App\Repositories\User;

interface UserInterface
{
    public function create($data);

    public function getUserByEmail($email);
}
