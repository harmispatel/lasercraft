<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ShopBanner, Category, ItemReview, Items};
use Magarrent\LaravelCurrencyFormatter\Facades\Currency;

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

        $averageRating = ItemReview::avg('rating');

        return view('frontend.product_detail',compact(['child_categories','item_details','related_items','averageRating']));
    }


    // Function for View Customer Cart
    function viewCart()
    {
        // Child Categories
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();

        return view('frontend.view_cart',compact(['child_categories']));
    }


    // Function for Send Item Review
    public function sendItemReview(Request $request)
    {

        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required|min:50',
        ];

       $request->validate($rules);

        try
        {
            $name = (isset($request->name)) ? $request->name : '';
            $item_id = (isset($request->item_id)) ? $request->item_id : '';
            $comment = (isset($request->message)) ? $request->message : '';
            $rating = (isset($request->rating)) ? $request->rating : '';
            $email = (isset($request->email)) ? $request->email : '';

            // Item Details
            $item = Items::where('id',$item_id)->first();
            $cat_id = (isset($item['category_id'])) ? $item['category_id'] : '';
            $user_ip = $request->ip();

            if($item->id)
            {
                $item_review = new ItemReview();
                $item_review->category_id = $cat_id;
                $item_review->name = $name;
                $item_review->item_id = $item_id;
                $item_review->rating = $rating;
                $item_review->ip_address = $user_ip;
                $item_review->comment = $comment;
                $item_review->email = $email;
                $item_review->save();

                return response()->json([
                    'success' => 1,
                    'message' => 'Your Review has been Submitted SuccessFully...',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => 0,
                    'message' => 'Internal Server Error!',
                ]);
            }

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }

    }


    // Function for Search Products
    function searchProducts(Request $request)
    {
        // Current Languge Code
        $current_lang_code = (session()->has('locale')) ? session()->get('locale') : 'en';

        // Client Settings
        $client_settings = getClientSettings();
        $default_currency = (isset($client_settings['default_currency'])) ? $client_settings['default_currency'] : 'USD';

        $keyword = $request->keywords;
        $name_key = $current_lang_code."_name";
        $html = '';

        try
        {
            $items_count = Items::where("$name_key",'LIKE','%'.$keyword.'%')->where('published',1)->count();
            $items = Items::with(['itemImages','itemPrices'])->where("$name_key",'LIKE','%'.$keyword.'%')->where('published',1)->get();

            if(!empty($keyword) && $items_count > 0)
            {
                $html .= '<div class="col-md-12">';
                    $html .= '<h4>'.$items_count.' Products for "'.$keyword.'".</h4>';
                $html .= '</div>';

                $html .= '<div class="col-md-12 mt-2">';
                    $html .= '<div class="product_items">';
                        foreach($items as $item)
                        {
                            $item_image = (isset($item->itemImages) && count($item->itemImages) > 0) ? $item->itemImages[0]->image : '';
                            $item_price = (isset($item->itemPrices) && count($item->itemPrices) > 0) ? $item->itemPrices[0]->price : 0.00;

                            $html .= '<a href="'.route('product.deatails',$item['id']).'">';
                                $html .= '<div class="product_box">';
                                    $html .= '<div class="product_image">';
                                        if(!empty($item_image) && file_exists('public/client_uploads/items/'.$item_image))
                                        {
                                            $html .= '<img src="'.asset('public/client_uploads/items/'.$item_image).'" class="w-100">';
                                        }
                                        else
                                        {
                                            $html .= '<img src="'.asset('public/client_images/not-found/no_image_1.jpg').'" class="w-100">';
                                        }
                                    $html .= '</div>';
                                    $html .= '<div class="product_info">';
                                        $html .= '<h3>'.$item[$name_key].'</h3>';
                                        $html .= '<p>'.Currency::currency($default_currency)->format($item_price).'</p>';
                                    $html .= '</div>';
                                $html .= '</div>';
                            $html .= '</a>';
                        }
                    $html .= '</div>';
                $html .= '</div>';
            }
            else
            {
                $html .= '<div class="col-md-12 text-center">';
                    $html .= '<h4>Records Not Found!</h4>';
                $html .= '</div>';
            }


            return response()->json([
                'success' => 1,
                'message' => 'Result has been Fetched SuccessFully...',
                'data' => $html,
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }

    }

}
