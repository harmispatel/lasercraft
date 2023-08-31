<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ShopBanner, Category};

class FrontendController extends Controller
{
    public function index()
    {
        $banners = ShopBanner::where('key','shop_banner')->get();
        $categories = Category::with(['child_categories'])->where('category_type','parent_category')->orderBy('order_key')->get();
        return view('frontend.index',compact('banners','categories'));

    }
}
