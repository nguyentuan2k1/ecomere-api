<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $table = "category";
    public $timestamps = false;

    protected $fillable = [
        "id",
        "name",
        "image",
        "order",
        "parent_category",
        "created_at",
        "updated_at",
    ];

    public function getImageAttribute()
    {
        if (!empty($this->attributes['image'])) return $this->attributes['image'] = getFileInStorage($this->attributes['image']);

        return $this->attributes['image'];
    }

    public function childs()
    {
        return $this->hasMany(Category::class, "parent_category", "id")->with('childs');
    }

    public function parents()
    {
        return $this->belongsTo(Category::class, "parent_category", "id")->with("parents");
    }
}
