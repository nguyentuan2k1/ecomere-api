<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $table = "reviews";

    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "user_id",
        "rating",
        "content",
        "images",
        "product_id",
        "created_at",
        "updated_at",
    ];

    protected $casts = [
        'images' => 'array',
    ];

    public function user()
    {
        return $this->hasOne(User::class, "id", "user_id")->select("id", "full_name", "avatar");
    }

    public function reviewHelpful()
    {
        $user = auth()->guard('api')->user();
        return $this->hasMany(ReviewHelpful::class, "review_id", "id")->where("user_id", $user->id);
    }

    public function getIsHelpfulAttribute()
    {
        return $this->attributes['is_helpful'] > 0;
    }

    public function getImagesAttribute()
    {
        if (empty($this->attributes['images'])) return null;

        $image = explode(",", $this->attributes['images']);

        foreach ($image as $key => $value) {
            $image[$key] = getUrlStorageFile($value);
        }

        $this->final_images = $image;
    }

    public function mapping($isNeedMappingIsHelpful = true)
    {
        if ($isNeedMappingIsHelpful) {
            $this->getIsHelpfulAttribute();
        }
        $this->user->getAvatarAttribute();
        $this->getImagesAttribute();
    }
}
