<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeValue extends Model
{
    use HasFactory;

    protected $table = "product_attribute_value";
    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "att_id",
        "value",
    ];

    public function attribute()
    {
        return $this->belongsTo(ProductAttribute::class, "att_id", "id");
    }
}
