<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewHelpful extends Model
{
    use HasFactory;

    protected $table = "review_helpful";

    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "review_id",
        "user_id",
        "product_id",
        "created_at",
        "updated_at",
    ];
}
