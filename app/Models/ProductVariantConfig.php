<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariantConfig extends Model
{
    use HasFactory;

    protected $primaryKey = null;
    protected $table      = "product-variant-config";
    public $incrementing  = false;
    public $timestamps    = false;

    protected $fillable = [
        "product_variant_id",
        "variant_option_id",
    ];
}
