<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ShopBanner, Category, Items};

class FrontendController extends Controller
{
    public function index()
    {
        $banners = ShopBanner::where('key','shop_banner')->get();
        $parent_categories = Category::with(['categoryImages'])->where('parent_id',NULL)->orderBy('order_key')->where('published',1)->get();
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();
        return view('frontend.index',compact('banners','parent_categories','child_categories'));
    }


    function collectionByCategory($catID)
    {
        $cat_details = Category::where('id',$catID)->first();
        $items = Items::with(['itemImages','itemPrices'])->where('category_id',$catID)->where('published',1)->get();
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();
        $sub_categories = Category::where('parent_id',$catID)->orderBy('order_key')->where('published',1)->get();

        return view('frontend.categories_collections',compact(['cat_details','items','child_categories','sub_categories']));
    }


    function productDetails($itemID)
    {
        // Item Details
        $item_details = Items::with(['itemImages','itemPrices'])->where('id',$itemID)->first();

        // Item Category ID
        $cat_id = (isset($item_details['category_id'])) ? $item_details['category_id'] : '';

        // Related Items
        $related_items = Items::with(['itemImages','itemPrices'])->where('id','!=',$itemID)->where('category_id',$cat_id)->where('published',1)->get();

        // Child Categories
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();

        return view('frontend.product_detail',compact(['child_categories','item_details','related_items']));
    }

}
