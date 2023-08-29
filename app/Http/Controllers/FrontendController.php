<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShopBanner;

class FrontendController extends Controller
{
    public function index()
    {
         $banners = ShopBanner::where('key','shop_banner')->get();
        return view('frontend.index',compact('banners'));

    }
}
