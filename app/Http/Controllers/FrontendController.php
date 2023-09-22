<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ShopBanner, Category, CustomerQuote, ItemReview, Items, User};
use Illuminate\Support\Facades\Validator;
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


    // Function for Contact US
    function contactUS()
    {
        // Child Categories
        $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();
        $contact_page = \App\Models\Category::where('category_type','check_in')->first();

        return view('frontend.contact_us',compact(['child_categories','contact_page']));
    }


    // Function for Submit Contact US
    function submitContactUS(Request $request)
    {
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email',
            'phone' => 'required|min:10',
            'company_name' => 'required',
            'message' => 'required|min:50',
            'document' => 'mimes:pdf,xls,csv,xlsx,jpg,jpeg,png,psd',
        ]);

        $shop_settings = getClientSettings();

        // CheckIN Mail Template
        $quote_mail_form = (isset($shop_settings['check_in_mail_form'])) ? $shop_settings['check_in_mail_form'] : '';

        $firstname = $request->firstname;
        $lastname = $request->lastname;
        $customer_mail = $request->email;
        $phone = $request->phone;
        $message = $request->message;
        $company_name = $request->company_name;

        $from_mail = $customer_mail;
        $subject = "New Customer Quote";

        $user_details = User::where('id',1)->where('user_type',2)->first();
        $contact_emails = (isset($user_details['contact_emails']) && !empty($user_details['contact_emails'])) ? unserialize($user_details['contact_emails']) : [];

        try
        {
            if(count($contact_emails) > 0 && !empty($quote_mail_form))
            {
                foreach($contact_emails as $mail)
                {
                    $to = $mail;

                    $html = $quote_mail_form;
                    $html = str_replace('{firstname}',$firstname,$html);
                    $html = str_replace('{lastname}',$lastname,$html);
                    $html = str_replace('{phone}',$phone,$html);
                    $html = str_replace('{company}',$company_name,$html);
                    $html = str_replace('{message}',$message,$html);

                    $headers = "MIME-Version: 1.0" . "\r\n";
                    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

                    // More headers
                    $headers .= 'From: <'.$from_mail.'>' . "\r\n";

                    mail($to,$subject,$html,$headers);
                }
            }

            // Insert Check In Info
            $new_customer_quote = new CustomerQuote();
            $new_customer_quote->firstname = $firstname;
            $new_customer_quote->lastname = $lastname;
            $new_customer_quote->email = $customer_mail;
            $new_customer_quote->phone = $phone;
            $new_customer_quote->company_name = $company_name;
            $new_customer_quote->message = $message;

            if($request->hasFile('document'))
            {
                $filename = "quote_file_".time().".". $request->file('document')->getClientOriginalExtension();
                $request->file('document')->move(public_path('client_uploads/customer_docs/'), $filename);
                $new_customer_quote->document = $filename;
            }

            $new_customer_quote->save();

            return redirect()->back()->with('success','Message has been Sent SuccessFully....');

        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }


    // Function for Print Details Page
    function printsPage($pageID)
    {
        try
        {
            // Child Categories
            $child_categories = Category::where('parent_id','!=',NULL)->orderBy('order_key')->where('published',1)->get();
            $page_details = Category::where('id',decrypt($pageID))->first();
            return view('frontend.print_page',compact(['child_categories','page_details']));
        }
        catch (\Throwable $th)
        {
            return redirect()->route('home')->with('error','Something Went Wrong !');
        }
    }

    function customerVerify($id)
    {
        try {

            $userID = decrypt($id);
            $user = User::find($userID);
            $user_verify = (isset($user['user_verify']) && $user['user_verify'] == 1) ? $user['user_verify'] : 0;

            if(!isset($user))
            {
                return redirect()->route('home')->with('error','User Not Found!');
            }

            if($user_verify == 1)
            {
                return redirect()->route('home')->with('success','Your Shop has been Already Registerd.');
            }
            else
            {
                $data['user_id'] = $id;
                return view('frontend.verify_customer',$data);
            }

        } catch (\Throwable $th) {
            return redirect()->route('home')->with('error','Something Went Wrong!');
        }
    }


    function customerVerifyPost(Request $request)
    {
        $request->validate([
            'verification_code' => 'required',
        ]);

        try {

            $user_id = decrypt($request->user_id);
            $verify_token = $request->verification_code;

            $user = User::find($user_id);
            $user_verify_token = (isset($user['verify_token'])) ? $user['verify_token'] : '';

            if(!empty($user_verify_token) && ($user_verify_token == $verify_token))
            {
                $user->verify_token = NULL;
                $user->user_verify = 1;
                $user->update();

                return redirect()->route('login')->with('success','Your Account has been Verified.');
            }
            else
            {
                $validator = Validator::make([], []);
                $validator->getMessageBag()->add('verification_code', 'Please Enter a Valid Token to Verify Your Account !');
                return redirect()->back()->withErrors($validator)->withInput();
            }

        } catch (\Throwable $th) {
            return redirect()->route('home')->with('error','Internal Server Error!');
        }
    }

}
