<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EarningCategory extends Model
{
    /** @use HasFactory<\Database\Factories\EarningCategoryFactory> */
    use HasFactory;

    public function earnings()
    {
        return $this->hasMany(Earning::class);
    }
}
