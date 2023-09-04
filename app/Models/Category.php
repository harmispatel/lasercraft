<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public function items()
    {
        return $this->hasMany(Items::class,'category_id','id')->where('published',1);
    }

    public function categoryImages()
    {
        return $this->hasMany(CategoryImages::class,'category_id','id');
    }

    // Get SubCategories
    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }
}
