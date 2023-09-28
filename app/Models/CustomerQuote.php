<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerQuote extends Model
{
    use HasFactory;

    function quotes_replys()
    {
        return $this->hasMany(CustomerQuoteReply::class,'quote_id','id');
    }
}
