<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserRepository implements UserInterface
{
    /**
     * Create user
     * @param $data
     * @return User|false
     */
    public function create($data)
    {
        try {
            $user = new User();

            foreach ($data as $field => $value) {
                $user->$field = $value;
            }

            $user->save();

            return $user;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    /**
     * Get user by email
     * @param string $email
     * @return mixed
     */
    public function getUserByEmail($email)
    {
        return User::where("email", $email)->first();
    }

    /**
     * Update user info by id
     * @param $data
     * @param $user_id
     * @return false
     */
    public function updateInfoById($data, $user_id)
    {
        try {
            $user = User::find($user_id);

            if (empty($user)) return false;

            foreach ($data as $field => $value) {
                $user->$field = $value;
            }

            $user->save();

            return $user;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    public function findUserByVerifyToken($token)
    {
        return User::where("verify_code", $token)->first();
    }
}
