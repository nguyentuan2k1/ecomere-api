<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    use HasFactory;
    protected $table = "product_attribute";
    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "name",
        "category_id",
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }
}
