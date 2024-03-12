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
       return $this->hasOne(User::class, "id", "user_id");
    }

    public function reviewHelpFul()
    {
        return $this->hasMany(ReviewHelpful::class, "review_id", "id")->where("user_id", 2);
    }
}
