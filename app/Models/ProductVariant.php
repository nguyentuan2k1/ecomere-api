<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ProductVariant extends Model
{
    use HasFactory;
    protected $table      = "product_variant";
    protected $primaryKey = "id";
    public $incrementing  = true;
    public $timestamps    = true;

    protected $fillable = [
        "price",
        "image",
        "sale_price",
        "product_id",
        "product_parent",
        "quantity",
        "active",
        "order",
    ];

    public function setCreatedAt()
    {
        return Carbon::now()->timestamp;
    }

    public function setUpdatedAt()
    {
        return Carbon::now()->timestamp;
    }
}
