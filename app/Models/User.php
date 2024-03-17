<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getAvatarAttribute()
    {
        return getUrlStorageFile($this->attributes['avatar'] ?? "");
    }

    public function getDOBAttribute()
    {
        $dateOfBirth = Carbon::parse($this->attributes['date_of_birth'] ?? null);
        return $dateOfBirth ? $dateOfBirth->format('d/m/Y') : "";
    }

    public function toUserDataApp($isNeedFormatAvatar = true){
        $data = $this->only(['full_name', 'email', 'avatar', 'date_of_birth']);

        $data['date_of_birth'] = $this->getDOBAttribute();

        if($isNeedFormatAvatar)
        {
            $data['avatar'] = $this->getAvatarAttribute();
        }

        return $data;
    }
}
