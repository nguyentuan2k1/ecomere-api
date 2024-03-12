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
        "product_id",
        "created_at",
        "updated_at",
    ];

    public function user()
    {
       return $this->hasOne(User::class, "id", "user_id")->select("id","full_name", "avatar");
    }

    public function reviewHelpful()
    {
        $user = auth()->guard('api')->user();
        return $this->hasOne(ReviewHelpful::class, "review_id", "id")->where("user_id", $user->id)->select("id", "review_id", "user_id");
    }
}
