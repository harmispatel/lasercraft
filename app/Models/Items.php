<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    use HasFactory;

    function category()
    {
        return $this->hasOne(Category::class,'id','category_id');
    }

    public function ratings()
    {
        return $this->hasMany(ItemReview::class,'item_id','id');
    }

    function itemImages() {
        return $this->hasMany(ItemImages::class,'item_id','id');
    }

    function itemPrices(){
        return $this->hasMany(ItemPrice::class,'item_id','id');
    }
}
