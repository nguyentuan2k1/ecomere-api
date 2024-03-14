<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Brand extends Model
{
    use HasFactory;
    protected $table      = "brands";
    protected $primaryKey = "id";
    public $incrementing  = true;

    protected $fillable = [
        "id",
        "name",
        "image",
        "created_at",
        "updated_at",
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
