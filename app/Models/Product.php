<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory;
    protected $table      = "products";
    public $timestamps    = true;
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

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id", "id");
    }

    public function setCreatedAt()
    {
        return Carbon::now()->timestamp;
    }

    public function setUpdatedAt()
    {
        return Carbon::now()->timestamp;
    }
}
