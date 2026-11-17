<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingContent extends Model
{
    protected $fillable = ['key', 'value', 'section', 'label'];

    // ✅ Get all as key => value array
    public static function allKeyed(): array
    {
        return self::pluck('value', 'key')->toArray();
    }
}
