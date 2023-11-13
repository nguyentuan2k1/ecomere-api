<?php

namespace App\Service\User;

use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Log;

class UserService
{
    public $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create a user with data
     * @param array $data
     * @return \App\Models\User|false
     */
    public function create($data)
    {
        return $this->userRepository->create($data);
    }

    /**
     * Get user by email
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        return $this->userRepository->getUserByEmail($email);
    }

    public function sendEmailForgotPassword($user, $token, $reset_link)
    {
        try {
            if (empty($user)) return false;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return true;
        }
    }

    public function sendVerifyEmail($user)
    {

    }

    public function updateInfoById($data, $user_id)
    {
        return $this->userRepository->updateInfoById($data, $user_id);
    }
}
