<?php

namespace App\Http\Controllers\Api\User;

use App\Enums\FileStorageDirectory;
use App\Enums\GeneralDefine;
use App\Enums\UserAvatar;
use App\Enums\UserDefine;
use App\Http\Controllers\BaseController;
use App\Mail\SendVerifyEmail;
use App\Service\PasswordReset\PasswordResetService;
use App\Service\UploadFile\UploadFileService;
use App\Service\User\UserService;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mockery\Exception;

class UserController extends BaseController
{
    public $userService;
    public $uploadFileService;
    public $passwordResetService;

    public function __construct(UserService $userService, UploadFileService $uploadFileService, PasswordResetService $passwordResetService)
    {
        $this->userService          = $userService;
        $this->uploadFileService    = $uploadFileService;
        $this->passwordResetService = $passwordResetService;
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

            if (!filter_var($user->avatar, FILTER_VALIDATE_URL)) {
                $user->avatar = !empty($user->avatar) ? getUrlStorageFile($user->avatar) : getUrlStorageFile(FileStorageDirectory::USER_AVATAR . "/" . UserAvatar::UserAvatarDefault)  ;
            }

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
                $validator = Validator::make($request->all(), [
                    "token" => ["required"]
                ], [
                    "*.required" => "This field is required",
                    "*.*"        => "This field is invalid"
                ]);

                if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

                $verifyGoogleData = VerifyGoogleToken($request->get("token"));

                if (empty($verifyGoogleData)) return $this->sendValidator(["token" => "Token is invalid"]);

                $user = $this->userService->getUserByEmail($verifyGoogleData->email);

                if (empty($user)) {
                    $data = [
                        "email"     => $verifyGoogleData->email,
                        "full_name" => $verifyGoogleData->name,
                        "password"  => Hash::make(Str::random("12")),
                        "type"      => "google",
                        "avatar"    => $verifyGoogleData->picture
                    ];

                    $user = $this->userService->create($data);

                    if (empty($user)) return $this->sendError("Create user failed", 400);
                }

                $userToken = $user->createToken("personal access token");

                $data = [
                    "access_token" => $userToken->accessToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => Carbon::parse($userToken->token->expires_at)->timestamp
                ];

                return $this->sendResponse($data, "Create Account Successfully !");
            } else {
                $validator = Validator::make($request->all(), [
                    "email"     => ["required", "max:" . GeneralDefine::MAX_LENGTH, "email:filter", "unique:users,email"],
                    "password"  => ["required", "min:" . UserDefine::MIN_PASSWORD, "max:" . UserDefine::MAX_PASSWORD],
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
            }

            $user = $this->userService->create($data);

            if (empty($user)) return $this->sendError("Create user failed", 400);

            $this->userService->sendVerifyEmail($user);

            return $this->sendResponse([], "Create Account Successfully ! Please check email verify");
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

            if (empty($user)) return $this->sendError("Email is not exist", 400);

            $token = Str::random("30");
            Mail::to("tteo@gmail.com")->send(new SendVerifyEmail("asdas", "aaaaa"));


            $this->passwordResetService->deleteAllToken($user->email);

            $passwordReset = $this->passwordResetService->create($user->email, $token);

            if (empty($passwordReset)) return $this->sendError("Can not create reset password", 400);

            $resetLink = env("APP_URL");
            $resetLink = "{$resetLink}/forgot-password/token?={$token}";

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
                "avatar"    => ["nullable", "url"],
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $dataUpdate = [
                "full_name" => $request->get("full_name"),
                "avatar"    => $request->get("avatar"),
            ];

            $update = $this->userService->updateInfoById($dataUpdate, $user->id);

            return $this->sendResponse($update, "Update user Success");
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

    public function updateAvatar(Request $request)
    {
        try {
            $user = auth()->guard('api')->user();

            if (empty($user)) return $this->sendError("Please Login Again", 401);

            $validator = Validator::make($request->all(), [
                "avatar" => ["required", 'file']
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $avatar          = $request->file("avatar");
            $typeFileAccepts = ["jpg", "png", "jpeg", "svg"];

            if (!in_array($avatar->getClientOriginalExtension(), $typeFileAccepts)) return $this->sendValidator(["avatar" => "File type is invalid"]);

            if ($avatar->getSize() > 10 * 1024 * 1024) return $this->sendValidator(["avatar" => "File size is invalid"]);

            $file = $avatar->getClientOriginalName();

            $filename  = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $filename  = vietnameseToLatin($filename) . "-" . time() . "." . $extension;
            $filePath  = $this->uploadFileService->uploadFile($avatar, $filename, "public/avatar");

            if (empty($filePath)) return $this->sendError("Can not upload your avatar", 400);

            if (!filter_var($filePath, FILTER_VALIDATE_URL)) $filePath = getUrlStorageFile($filePath);

            return $filePath;
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }
}
