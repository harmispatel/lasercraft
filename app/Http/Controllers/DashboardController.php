<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Items;
use App\Models\Languages;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Display Client Dashboard
    public function clientDashboard()
    {
        // Get Language Settings
        $language_settings = clientLanguageSettings();
        $primary_lang_id = isset($language_settings['primary_language']) ? $language_settings['primary_language'] : '';

        // Primary Language Details
        $data['primary_language_detail'] = Languages::where('id',$primary_lang_id)->first();

        // Total Category Count
        $category['total_category'] = Category::whereIn('category_type',['product_category','parent_category'])->count();
        $category['total_page'] = Category::where('category_type','page')->count();
        $category['total_link'] = Category::where('category_type','link')->count();
        $category['gallery'] = Category::where('category_type','gallery')->count();
        $category['pdf_page'] = Category::where('category_type','pdf_page')->count();
        $category['check_in'] = Category::where('category_type','check_in')->count();

        // All Categories List
        $data['categories'] = Category::with(['categoryImages'])->limit(8)->latest('created_at')->get();

        // Get All Items
        $data['items'] = Items::with(['category','itemImages'])->limit(8)->latest('created_at')->get();

        // Total Food Count
        $item['total'] = Items::count();

        $data['category'] = $category;
        $data['item'] = $item;

        return view('client.dashboard.dashboard',$data);
    }

    // Function for Change Backend Language
    public function changeBackendLanguage(Request $request)
    {
        $lang_code = $request->langCode;

        session()->put('lang_code',$lang_code);

        return response()->json([
            'success' => 1,
        ]);
    }
}
