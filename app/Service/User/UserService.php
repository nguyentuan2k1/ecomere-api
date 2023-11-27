<?php

namespace App\Service\User;

use App\Mail\SendVerifyEmail;
use App\Repositories\User\UserRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
        try {
            if (empty($user)) return false;

            $link     = "http://127.0.0.1:8000/verify_email?verify_code={$user->verify_code}";
            $mailView = new SendVerifyEmail($user, $link);
            $mailSend = Mail::to($user->email)->send($mailView);

            return $mailSend;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    public function updateInfoById($data, $user_id)
    {
        return $this->userRepository->updateInfoById($data, $user_id);
    }

    /**
     * Find first by verify token
     * @param $token
     * @return mixed
     */
    public function findUserByVerifyToken($token)
    {
        return $this->userRepository->findUserByVerifyToken($token);
    }
}
