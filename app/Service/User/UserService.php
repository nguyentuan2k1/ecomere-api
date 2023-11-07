<?php

namespace App\Service\User;

use App\Repositories\User\UserRepository;

class UserService
{
    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function create($data)
    {
        return $this->userRepository->create($data);
    }

    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }
}
