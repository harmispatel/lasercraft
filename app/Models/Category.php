<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(Items::class,'category_id','id')->where('published',1)->orderBy('order_key','ASC');
    }

    public function categoryImages()
    {
        return $this->hasMany(CategoryImages::class,'category_id','id');
    }

    public function parentCategory()
    {
        return $this->hasOne(Category::class,'id','parent_id');
    }

    // Get SubCategories
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id')->where('published',1);
    }
}
