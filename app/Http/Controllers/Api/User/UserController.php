<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Http\Middleware\TrustHosts;
use App\Service\PasswordReset\PasswordResetService;
use App\Service\UploadFile\UploadFileService;
use App\Service\User\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Mockery\Exception;

class UserController extends BaseController
{
    public $userService;
    public $uploadFileService;
    public $passwordResetService;

    public function __construct(
        UserService $userService,
        UploadFileService $uploadFileService,
        PasswordResetService $passwordResetService
    ) {
        $this->userService          = $userService;
        $this->uploadFileService    = $uploadFileService;
        $this->passwordResetService = $passwordResetService;
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "email:filter"],
                "password"  => ["required"],
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            if (Auth::attempt(["email" => $request->get("email"), "password" => $request->get("password")])) {
                $user = $this->userService->getUserByEmail($request->get("email"));

                if (empty($user)) return $this->sendError("Login fails", 400);

                if (empty($user->email_verified_at)) return $this->sendError("Please verify your email before login", 400);

                $userToken = $user->createToken("personal access token");

                if (!filter_var($user->avatar, FILTER_VALIDATE_URL))
                    $user->avatar = !empty($user->avatar) ? getUrlStorageFile($user->avatar) : getUrlStorageFile(config("generate.file_storage_directory.avatar") . "/" .  config("generate.user.avatar.default"));

                $data = [
                    "user_info"    => $user->toUserDataApp(),
                    "access_token" => $userToken->accessToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => Carbon::parse($userToken->token->expires_at)->timestamp
                ];

                return $this->sendResponse($data);
            }

            return $this->sendError("email or password is incorrect", 401);
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

            if (!filter_var($user->avatar, FILTER_VALIDATE_URL))
                $user->avatar = !empty($user->avatar) ? getUrlStorageFile($user->avatar) : getUrlStorageFile(config("generate.file_storage_directory.avatar") . "/" .  config("generate.user.avatar.default"));

            return $this->sendResponse($user->toUserDataApp(false));
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function registerBySocial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "register_type" => ["required"]
        ], [
            "register_type.required" => "Register Type is required",
        ]);

        if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

        $registerTypeAccepts = array_values(config("generate.social_accepts"));

        if (in_array($request->get("register_type"), $registerTypeAccepts)) {
            if ($request->get("register_type") == config("generate.social_accepts.google")) {
                if (empty($request->get("token"))
                    || strlen(trim($request->get("token") <= 0))
                ) {
                    return $this->sendValidator(["token" => "Token is required"]);
                }

                $verifyGoogleData = VerifyGoogleToken($request->get("token"));

                if (empty($verifyGoogleData)) return $this->sendValidator(["token" => "Token is invalid"]);

                $user = $this->userService->getUserByEmail($verifyGoogleData->email);

                if (empty($user)) {
                    $data = [
                        "email"             => $verifyGoogleData->email,
                        "full_name"         => $verifyGoogleData->name,
                        "password"          => Hash::make(Str::random("12")),
                        "type"              => $request->get("register_type"),
                        "avatar"            => $verifyGoogleData->picture,
                        "email_verified_at" => Carbon::now()->timestamp,
                    ];

                    $user = $this->userService->create($data);

                    if (empty($user)) return $this->sendError("Create user failed", 400);
                }

                if (empty($user->email_verified_at)) $this->userService->updateInfoById(["email_verified_at" => Carbon::now()->timestamp], $user->id);

                $userToken = $user->createToken("personal access token");

                $user = $user->toUserDataApp(false);

                if (!empty($user['avatar'])
                    && !filter_var($user['avatar'], FILTER_VALIDATE_URL)
                ) {
                    $user['avatar'] = getUrlStorageFile($user['avatar']);
                }

                $data = [
                    "user_info"    => $user,
                    "access_token" => $userToken->accessToken,
                    'token_type'   => 'Bearer',
                    'expires_at'   => Carbon::parse($userToken->token->expires_at)->timestamp
                ];

                return $this->sendResponse($data, "Create Account Successfully !");
            }
        }

        return $this->sendError("Register type is not support", 400);
    }

    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "email"     => ["required", "max:" . config("generate.max_length"), "email:filter", "unique:users,email"],
                "password"  => ["required", "min:" . config("generate.user.password.min"), "max:" . config("generate.user.password.max")],
                "full_name" => ["required", "min:2", "max:" . config("generate.max_length")],
            ], [
                "email.required"     => "Email is required",
                "password.required"  => "Password is required",
                "full_name.required" => "Full Name is required",
                "full_name.min"      => "Full Name is required",
                "full_name.max"      => "Full Name only accept " . config("generate.max_length") . " character",
                "email.max"          => "Email only accept" . config("generate.max_length") . "character",
                "email.email"        => "Email is invalid",
                "email.unique"       => "Email is duplicate with another user",
                "password.min"       => "Password must be ". config("generate.user.password.min") ." character",
                "password.max"       => "Password only accept ". config("generate.user.password.max") ." character",
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $data = [
                "email"       => $request->get("email"),
                "full_name"   => $request->get("full_name"),
                "password"    => Hash::make($request->get("password")),
                "type"        => "password",
                "verify_code" => sha1(Str::random(12). time()),
            ];

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
                "email"     => ["required", "email:filter", "max:" . config("generate.max_length")],
            ], [
                "*.required" => "This field is required",
                "*.*"        => "This field is invalid"
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $user = $this->userService->getUserByEmail($request->get("email"));

            if (empty($user)) return $this->sendError("Email is not exist", 400);

            $token = Str::random("30");

            $this->passwordResetService->deleteAllToken($user->email);

            $passwordReset = $this->passwordResetService->create($user->email, $token);

            if (empty($passwordReset)) return $this->sendError("Can not create reset password", 400);

            $resetLink = config("generate.url_mobile_app");
            $resetLink = "{$resetLink}forgot-password?token={$token}";
            $resetLink = makeShortUrl($resetLink);

            if (empty($resetLink)) return $this->sendError("Can not create reset password link", 400);

            $this->userService->sendEmailForgotPassword($user, $resetLink);

            return $this->sendResponse("Send Email to user successfully");
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
                "full_name" => ["required", "max:" . config("generate.max_length")],
                "avatar"    => ["nullable", "url"],
                "date_of_birth" => [
                    "nullable",
                    "integer",
                    "date_format:U",
                    "before_or_equal:" .Carbon::now()->subYears(13)->timestamp,
                ],
            ], [
                "*.required" => "This field is required",
                "avatar.url" => "This field is invalid",
                "full_name.max" => "The :attribute only accept " . config("generate.max_length") . " character",
                "date_of_birth.integer" => "The :attribute must be a valid timestamp.",
                "date_format" => "The :attribute must be a valid timestamp.",
                "before_or_equal" => "The :attribute must be before 13 years ago.",
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $dataUpdate = [
                "full_name" => $request->get("full_name"),
            ];

            if($request->get("avatar") != null)
            {
                $dataUpdate["avatar"] = $request->get("avatar");
            }

            if($request->get("date_of_birth") != null)
            {
                $dataUpdate["date_of_birth"] = Carbon::createFromTimestamp($request->get("date_of_birth"))->toDateString();
            }

            $updateUser = $this->userService->updateInfoById($dataUpdate, $user->id);

            return $this->sendResponse($updateUser->toUserDataApp(), "Update user Success");
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
                "new_password" => ["required", "min:" . config("generate.user.password.min"), "max:" . config("generate.user.password.max")],
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
            $typeFileAccepts = config("generate.file_type_accept.avatar");

            if (!in_array($avatar->getClientOriginalExtension(), $typeFileAccepts)) return $this->sendValidator(["avatar" => "File type is invalid"]);

            if ($avatar->getSize() > 10 * 1024 * 1024) return $this->sendValidator(["avatar" => "File size is invalid"]);

            $file      = $avatar->getClientOriginalName();
            $filename  = pathinfo($file, PATHINFO_FILENAME);
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $filename  = Str::slug($filename) . "-" . time() . "." . $extension;
            $filePath  = $this->uploadFileService->uploadFile($avatar, $filename, config("generate.file_storage_directory.avatar"));

            if (empty($filePath)) return $this->sendError("Can not upload your avatar", 400);

            if (!empty($user->avatar)
                && Storage::exists($user->avatar)
            )  {
                Storage::delete($user->avatar);
            }

            $data = [
                "avatar" => $filePath
            ];

            $updateUser = $this->userService->updateInfoById($data, $user->id);

            if (!$updateUser) {
                if (Storage::exists($filePath)) Storage::delete($filePath);

                return $this->sendError("Update Avatar Failed");
            }

            return $this->sendResponse($updateUser->toUserDataApp(), "Update Avatar Successfully");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return false;
        }
    }

    public function verifyTokenEmail(Request $request)
    {
        $urlMobile = config("generate.url_mobile_app") . "verify_email";
        $message   = "";

        if (empty($request->get("verify_code"))) {
            $message = "Verify Code is required";
        } else {
            $user = $this->userService->findUserByVerifyToken($request->get("verify_code"));

            if (empty($user)) {
                $message = "Verify Code is invalid";
            } else {
                if ($user->email_verified_at != null) {
                    $message = "Your email has been verify";
                } else {
                    $data = [
                        "email_verified_at" => Carbon::now()->timestamp
                    ];

                    $update = $this->userService->updateInfoById($data, $user->id);

                    if (!empty($update)) $message = "Verify Successfully";
                }
            }
        }

       return view("VerifyEmailToken")->with([
           "message"       => $message,
           "url_go_to_app" => $urlMobile,
       ]);
    }

    public function verifyTokenReset(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "token" => ["required"],
            ], [
                "*.required" => "This field is required",
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $resetPassword = $this->passwordResetService->getPasswordResetByToken($request->get("token"));

            if (empty($resetPassword)) return $this->sendError("Token is invalid", 400);

            if (Carbon::now()->timestamp > $resetPassword->created_at + config("generate.reset_token_time")) return $this->sendError("Token is expired", 400);

            return $this->sendResponse([], "Your Token is good");
        } catch (Exception $exception) {
            Log::error($exception->getMessage());

            return $this->sendError($exception->getMessage(), 500);
        }
    }

    public function resetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "token"        => ["required"],
                "new_password" => ["required", "min:" . config("generate.user.password.min"), "max:" . config("generate.user.password.max")]
            ], [
                "*.required" => "This field is required",
            ]);

            if ($validator->fails()) return $this->sendValidator($validator->errors()->toArray());

            $resetPassword = $this->passwordResetService->getPasswordResetByToken($request->get("token"));

            if (empty($resetPassword)) return $this->sendError("Token is invalid", 400);

            if (Carbon::now()->timestamp > $resetPassword->created_at + config("generate.reset_token_time")) return $this->sendError("Token is expired", 400);

            $dataUpdate = [
                "password" => Hash::make($request->get("new_password")),
            ];

            $user = $this->userService->getUserByEmail($resetPassword->email);

            if ($user->email_verified_at == null) {
                $updateVerifyEmail = [
                    "email_verified_at" => Carbon::now()->timestamp,
                ];

                $dataUpdate = array_merge($dataUpdate, $updateVerifyEmail);
            }

            $update = $this->userService->updateInfoById($dataUpdate, $user->id);

            if (!$update) return $this->sendError("Update Password fail", 400);

            $this->passwordResetService->deleteAllToken($user->email);

            return $this->sendResponse([],"Update Password Successfully");
        } catch (Exception $exception){
            Log::error($exception->getMessage());

            return false;
        }
    }
}
