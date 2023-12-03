<?php

namespace App\Service\User;

use App\Jobs\SendEmailJob;
use App\Mail\SendResetPasswordEmail;
use App\Mail\SendVerifyEmail;
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

    public function sendEmailForgotPassword($user, $reset_link)
    {
        try {
            if (empty($user)) return false;

            $resetPasswordMail = new SendResetPasswordEmail($user, $reset_link);
            SendEmailJob::dispatch($user, $resetPasswordMail);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return true;
        }
    }

    public function sendVerifyEmail($user)
    {
        try {
            if (empty($user)) return false;

            $link     = env("APP_URL") . "verify_email?verify_code={$user->verify_code}";
            $mailView = new SendVerifyEmail($user, $link);
            SendEmailJob::dispatch($user, $mailView);

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
