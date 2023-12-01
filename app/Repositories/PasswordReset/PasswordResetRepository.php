<?php

namespace App\Repositories\PasswordReset;

use App\Models\PasswordReset;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class PasswordResetRepository implements PasswordResetInterface
{
    /**
     * Create password reset
     * @param $email
     * @param $token
     * @return PasswordReset|false
     */
   public function create($email, $token)
   {
       try {
           $passwordReset             = new PasswordReset();
           $passwordReset->email      = $email;
           $passwordReset->token      = $token;
           $passwordReset->created_at = Carbon::now()->timestamp;

           $passwordReset->save();

           return $passwordReset;
       } catch (\Exception $exception) {
           Log::error($exception->getMessage());

           return false;
       }
   }

    /**
     * Delete all reset password
     * @param $email
     * @return false
     */
   public function DeleteAllResetPasswordByEmail($email)
   {
       try {
           return PasswordReset::where("email", $email)->delete();

       } catch (\Exception $exception) {
           Log::error($exception->getMessage());

           return false;
       }
   }

    /**
     * Get password reset
     * @param $email
     * @return mixed
     */
   public function getPasswordResetByEmail($email)
   {
       return PasswordReset::where("email", $email)->first();
   }

   public function getPasswordResetByToken($token)
   {
       return PasswordReset::where("token", $token)->first();
   }
}
