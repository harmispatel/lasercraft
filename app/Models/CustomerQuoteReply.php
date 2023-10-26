<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerQuoteReply extends Model
{
    use HasFactory;

    function customer_quote() {
        return $this->hasOne(CustomerQuote::class, 'id', 'quote_id');
    }
}
