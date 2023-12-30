<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;
    protected $table = "banners";
    protected $primaryKey = "id";

    protected $fillable = [
        "id",
        "name",
        "banner_link",
        "is_home",
        "image",
        "expired_at",
        "order",
        "active",
    ];

    public function getImageAttribute()
    {
        if (!empty($this->attributes['image'])) return $this->attributes['image'] = getFileInStorage($this->attributes['image']);

        return $this->attributes['image'];
    }
}
