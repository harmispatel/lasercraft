<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionPrice extends Model
{
    use HasFactory;

    public function option()
    {
        return $this->hasOne(Option::class,'id','option_id');
    }
}
