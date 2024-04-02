<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory;
    protected $table      = "products";
    public $timestamps    = false;
    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "name",
        "category_id",
        "rating",
        "description",
        "brand_id",
        "image",
        "sale_price",
        "price",
        "order",
        "active",
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
