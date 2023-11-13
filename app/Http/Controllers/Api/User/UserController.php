<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\FileStorageDirectory;
use App\Enums\GeneralDefine;
use App\Enums\UserAvatar;
use App\Enums\UserDefine;
use App\Http\Controllers\BaseController;
use App\Service\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mockery\Exception;

class UserController extends BaseController
{
    public $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email:filter"],
                "password"  => ["required", "min:" . UserDefine::MIN_PASSWORD, "max:". UserDefine::MAX_PASSWORD],
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            if (Auth::attempt(["email" => $request->get("email"), "password" => $request->get("password")])) {
                $user = $this->userService->getUserByEmail($request->get("email"));

                if (empty($user)) return $this->sendError("Login fails", 400);

                $userToken = $user->createToken("personal access token");

                $data = [
                    "access_token" => $userToken->accessToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => Carbon::parse($userToken->token->expires_at)->timestamp
                ];

                return $this->sendResponse($data);
            }

            return $this->sendError("email or password is incorrect", 400);
        } catch (Exception $exception){
            Log::error($exception->getMessage());

            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function info()
    {
        try {
            $user = auth()->guard('api')->user();

            if (empty($user)) return $this->sendError("user data error", 401);

            $user->avatar = !empty($user->avatar) ? getUrlStorageFile($user->avatar) : getUrlStorageFile(FileStorageDirectory::USER_AVATAR . "/" . UserAvatar::UserAvatarDefault)  ;

            $user = $user->only([
                "full_name",
                "email",
                "avatar"
            ]);

            return $this->sendResponse($user);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function register(Request $request)
    {
        try {
            if ($request->get("register_type") == "google") {

            } else {
                $validator = Validator::make($request->all(), [
                    "email"     => ["required", "max:" . GeneralDefine::MAX_LENGTH, "email:filter", "unique:users,email"],
                    "password"  => ["required", "min:" . UserDefine::MIN_PASSWORD, "max:". UserDefine::MAX_PASSWORD],
                    "full_name" => ["required", "min:2", "max:" . GeneralDefine::MAX_LENGTH],
                ], [
                    "*.required" => "This field is required",
                    "*.*"        => "This field is invalid"
                ]);

                if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

                $data = [
                    "email"     => $request->get("email"),
                    "full_name" => $request->get("full_name"),
                    "password"  => Hash::make($request->get("password")),
                    "type"      => "password"
                ];

                $user = $this->userService->create($data);

                if (empty($user)) return $this->sendError("Create user failed", 400);

                $this->userService->sendVerifyEmail($user);
            }

            return $this->sendResponse($data);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function logout()
    {
        try {
            $user = auth()->guard('api')->user();

            if (empty($user)) return $this->sendError("Please Login Again", 401);

            $user->token()->revoke();

            return $this->sendResponse("Logout Successfully");
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError($exception->getMessage(), 500);
        }
    }

    // Chưa xong
    public function forgotPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email:filter", "max:" . GeneralDefine::MAX_LENGTH],
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $user = $this->userService->getUserByEmail($request->get("email"));

            if (empty($user)) return $this->sendError("Email is invalid", 400);

            $token     = Str::random("30");
            $resetLink = env("APP_URL");
            $resetLink =  "{$resetLink}/forgot-password/token?={$token}";
            $this->userService->sendEmailForgotPassword($user, $token, $resetLink);
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    public function updateInfo(Request $request)
    {
        try {
            $user = auth()->guard('api')->user();

            if (empty($user)) return $this->sendError("Please Login Again", 401);

            $validator = Validator::make($request->all(), [
                "full_name" => ["required", "max:" . GeneralDefine::MAX_LENGTH],
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $user = auth()->guard('api')->user();

            if (empty($user)) return $this->sendError("Please Login Again", 401);

            $validator = Validator::make($request->all(), [
                "old_password" => ["required"],
                "new_password" => ["required", "min:" . UserDefine::MIN_PASSWORD, "max:" . UserDefine::MAX_PASSWORD],
            ], [
                "*.required" => "This field is required",
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            if (!Hash::check($request->get("old_password"), $user->password))
                return $this->sendValidator(["old_password" => "old password is not incorrect"]);

            $data = [
                "password" => Hash::make($request->get("new_password")),
            ];

            $update = $this->userService->updateInfoById($data, $user->id);

            if (empty($update)) return $this->sendError("Update Failed");

            return $this->sendResponse([], "Update Successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }
}
