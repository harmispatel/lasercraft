<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ClientSettings, Country, Ingredient, LanguageSettings, OrderSetting, PaymentSettings, QrSettings, Subscriptions,Shop, Theme, ThemeSettings, User, UserShop, UsersSubscriptions};
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class FrontendController extends Controller
{

    public function index()
    {
        $data['shops'] = Shop::with(['usershop'])->whereHas('usershop',function($q) {
            $q->whereHas('user',function($r) {
                $r->where('is_fav',1);
            });
        })->latest()->take(10)->get();
        return view('frontend.index',$data);
    }


    public function pricing()
    {
        $data['subscriptions'] = Subscriptions::get();
        return view('frontend.pricing_list',$data);
    }

    public function contactUS()
    {
        return view('frontend.cotact_us');
    }

    public function contactUSMail(Request $request)
    {
        try
        {
            $to = 'info@smartqrscan.com';
            $from = $request->email;
            $message = $request->message;
            $mobile_number = $request->mobile_number;
            $business_name = $request->bussiness_name;
            $name = $request->name;
            $subject = 'Contact US';

            $html = "<h4>From - ".$name."</h4>";
            $html .= "<h4>Mobile - ".$mobile_number."</h4>";
            $html .= "<h4>Business Name - ".$business_name."</h4>";
            $html .= "<p>".$message."</p>";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            // More headers
            $headers .= 'From: <'.$from.'>' . "\r\n";

            mail($to,$subject,$html,$headers);

            return redirect()->back()->with('success','Mail has been Sent SuccessFully...');
        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error','Internal Server Error!');
        }
    }

    public function QrGuide()
    {
        return view('frontend.qr_guide');
    }

    public function QrMenu()
    {
        return view('frontend.qr_menu');
    }

    public function signUpTrial()
    {
        $data['countries'] = Country::get();
        $data['subscriptions'] = Subscriptions::where('id',5)->get();

        $operators = ['+', '-', '*'];
        $operator = $operators[array_rand($operators)];
        $number1 = rand(1, 100);
        $number2 = rand(1, 100);
        $result = eval("return $number1 $operator $number2;");

        session()->put('captcha_answer', $result);

        $data['number1'] = $number1;
        $data['number2'] = $number2;
        $data['operator'] = $operator;

        return view('frontend.signup_trial',$data);
    }

    public function registerShopFrontend(Request $request)
    {
        $captchaAnswer = session()->get('captcha_answer');
        $userResponse = $request->captcha_response;

        $request->validate([
            'firstname' => 'required',
            'subscription' => 'required',
            'captcha_response' => 'required',
            'shop_name' => 'required',
            'city' => 'required',
            'country' => 'required',
            'pincode' => 'required',
            'address' => 'required',
            'shop_url' => 'required|regex:/^[a-zA-Z-]+$/|unique:shops,shop_slug',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'mobile_number' => 'required|digits:10',
            'catalogue' => 'mimes:xls,csv,xlsx,pdf',
        ]);

        if ($userResponse == $captchaAnswer)
        {
            $firstname = $request->firstname;
            $lastname = $request->lastname;
            $shop_slug = $request->shop_url;
            $shop_name = $request->shop_name;
            $address = (isset($request->address)) ? $request->address : NULL;
            $city = (isset($request->city)) ? $request->city : NULL;
            $country = (isset($request->country)) ? $request->country : NULL;
            $pincode = (isset($request->pincode)) ? $request->pincode : NULL;
            $mobile = (isset($request->mobile_number)) ? $request->mobile_number : NULL;
            $email = $request->email;
            $subscription_id = $request->subscription;
            $primary_language = 1;
            $user_verify_token = genratetoken(8);
            $html = '';
            $password = Hash::make($request->password);

            $subscription = Subscriptions::where('id',$subscription_id)->first();
            $subscription_duration = isset($subscription->duration) ? $subscription->duration : '';

            $date = Carbon::now();
            $current_date = $date->toDateTimeString();
            $end_date = $date->addMonths($subscription_duration)->toDateTimeString();
            $duration = $subscription_duration.' Months.';

            // Insert New Client
            $client = new User();
            $client->firstname = $firstname;
            $client->lastname = $lastname;
            $client->email = $email;
            $client->mobile = $mobile;
            $client->zipcode = $pincode;
            $client->country = $country;
            $client->city = $city;
            $client->address = $address;
            $client->password = $password;
            $client->status = 0;
            $client->user_type = 2;
            $client->is_fav = 0;
            $client->user_verify = 0;
            $client->verify_token = $user_verify_token;
            $client->save();

            if($client->id)
            {
                // Insert Client Shop
                $shop = new Shop();
                $shop->name = $shop_name;
                $shop->shop_slug = $shop_slug;

                // Make Shop Directory
                mkdir(public_path('client_uploads/shops/'.$shop_slug));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/banners"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/ingredients"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/categories"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/intro_icons"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/items"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/theme_preview_image"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/today_special_icon"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/top_logos"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/tables"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/rooms"));
                mkdir(public_path('client_uploads/shops/'.$shop_slug."/catalogue"));

                $shop->save();

                // Upload Catalogue
                if($request->hasFile('catalogue'))
                {
                    $catalogue_name = $shop_slug."_catalogue_".time().".". $request->file('catalogue')->getClientOriginalExtension();
                    $request->file('catalogue')->move(public_path('client_uploads/shops/'.$shop_slug.'/catalogue/'), $catalogue_name);

                    // Update User Settings
                    $edit_client = User::find($client->id);
                    $edit_client->catalogue = $catalogue_name;
                    $edit_client->update();
                }

                // Shop's Default Currency
                $default_currency = new ClientSettings();
                $default_currency->client_id = $client->id;
                $default_currency->shop_id = $shop->id;
                $default_currency->key = 'default_currency';
                $default_currency->value = "INR";
                $default_currency->save();

                // Business Name
                $business_name = new ClientSettings();
                $business_name->client_id = $client->id;
                $business_name->shop_id = $shop->id;
                $business_name->key = 'business_name';
                $business_name->value = $shop_name;
                $business_name->save();

                // Generate Shop Qr
                $new_shop_url = URL::to('/')."/".$shop_slug;
                $qr_name = $shop_slug."_".time()."_qr.svg";
                $upload_path = public_path('admin_uploads/shops_qr/'.$qr_name);

                QrCode::format('svg')->margin(2)->size(200)->generate($new_shop_url, $upload_path);

                // Insert Qr Code Settings
                $qrdata = [
                    'qr_size' => '200',
                    'qr_style' => 'square',
                    'eye_style' => 'square',
                    'color_type' => '',
                    'color_transparent' => 100,
                    'background_color_transparent' => 100,
                    'eye_inner_color' => "#000000",
                    'eye_outer_color' => "#000000",
                    'first_color' => "#000000",
                    'second_color' => "#000000",
                    'background_color' => "#ffffff",
                ];

                $qr_setting = new QrSettings();
                $qr_setting->shop_id = $shop->id;
                $qr_setting->value = serialize($qrdata);
                $qr_setting->save();

                // Update Shop Details
                $update_shop_dt = Shop::find($shop->id);
                $update_shop_dt->qr_code = $qr_name;
                $update_shop_dt->update();

                // Insert Default Themes
                $def_themes = [
                    'Default Light Theme',
                    'Default Dark Theme',
                ];

                foreach ($def_themes as $key => $value)
                {
                    $theme = new Theme();
                    $theme->shop_id = $shop->id;
                    $theme->name = $value;
                    $theme->is_default = 1;
                    $theme->save();

                    // Insert Theme Settings
                    if($value == 'Default Light Theme')
                    {
                        $setting_keys = [
                            'header_color' => '#ffffff',
                            'sticky_header' => 1,
                            'language_bar_position' => 'left',
                            'logo_position' => 'center',
                            'search_box_position' => 'right',
                            'banner_position' => 'top',
                            'banner_type' => 'image',
                            'banner_slide_button' => 1,
                            'banner_delay_time' => 3000,
                            'background_color' => '#ffffff',
                            'font_color' => '#4d572b',
                            'label_color' => '#ffffff',
                            'social_media_icon_color' => '#4d572b',
                            'categories_bar_color' => '#ffffff',
                            'menu_bar_font_color' => '#4d572b',
                            'category_title_and_description_color' => '#4d572b',
                            'price_color' => '#000000',
                            'item_box_shadow' => 1,
                            'item_box_shadow_color' => '#d1ccb8',
                            'item_box_shadow_thickness' => '5px',
                            'item_divider' => 1,
                            'item_divider_color' => '#000000',
                            'item_divider_thickness' => '5',
                            'item_divider_type' => 'solid',
                            'item_divider_position' => 'top',
                            'item_divider_font_color' => '#4d572b',
                            'tag_font_color' => '#4d572b',
                            'tag_label_color' => '#ffffff',
                            'category_bar_type' => '8px',
                            'search_box_icon_color' => '#000000',
                            'read_more_link_color' => '#0000ff',
                            'read_more_link_label' => 'Read More',
                            'banner_height' => '350',
                            'label_color_transparency' => 1,
                            'item_box_background_color' => '#ffffff',
                            'item_title_color' => '#4d572b',
                            'item_description_color' => '#000000',
                        ];

                        foreach($setting_keys as $key => $val)
                        {
                            $theme_setting = new ThemeSettings();
                            $theme_setting->theme_id = $theme->id;
                            $theme_setting->key = $key;
                            $theme_setting->value = $val;
                            $theme_setting->save();
                        }

                        // Client's Active Theme
                        $active_theme = new ClientSettings();
                        $active_theme->client_id = $client->id;
                        $active_theme->shop_id = $shop->id;
                        $active_theme->key = 'shop_active_theme';
                        $active_theme->value = $theme->id;
                        $active_theme->save();
                    }
                    else
                    {
                        $setting_keys = [
                            'header_color' => '#000000',
                            'sticky_header' => 1,
                            'language_bar_position' => 'left',
                            'logo_position' => 'center',
                            'search_box_position' => 'right',
                            'banner_position' => 'top',
                            'banner_type' => 'image',
                            'banner_slide_button' => 1,
                            'banner_delay_time' => 3000,
                            'background_color' => '#000000',
                            'font_color' => '#ffffff',
                            'label_color' => '#000000',
                            'social_media_icon_color' => '#ffffff',
                            'categories_bar_color' => '#000000',
                            'menu_bar_font_color' => '#E7B76B',
                            'category_title_and_description_color' => '#ffffff',
                            'price_color' => '#E7B76B',
                            'item_box_shadow' => 1,
                            'item_box_shadow_color' => '#E7B76B',
                            'item_box_shadow_thickness' => '5px',
                            'item_divider' => 1,
                            'item_divider_color' => '#ffffff',
                            'item_divider_thickness' => '3',
                            'item_divider_type' => 'dotted',
                            'item_divider_position' => 'bottom',
                            'item_devider_font_color' => '#ffffff',
                            'tag_font_color' => '#ffffff',
                            'tag_label_color' => '#000000',
                            'search_box_icon_color' => '#ffffff',
                            'read_more_link_color' => '#9f9f9f',
                            'read_more_link_label' => 'Read More',
                            'banner_height' => '350',
                            'label_color_transparency' => 1,
                            'item_box_background_color' => '#000000',
                            'item_title_color' => '#ffffff',
                            'item_description_color' => '#ffffff',
                        ];

                        foreach($setting_keys as $key => $val)
                        {
                            $theme_setting = new ThemeSettings();
                            $theme_setting->theme_id = $theme->id;
                            $theme_setting->key = $key;
                            $theme_setting->value = $val;
                            $theme_setting->save();
                        }
                    }

                }


                // Insert Order Settings
                $order_settings_keys = [
                    'delivery' => 0,
                    'takeaway' => 0,
                    'room_delivery' => 0,
                    'table_service' => 0,
                    'only_cart' => 0,
                    'auto_order_approval' => 0,
                    'scheduler_active' => 0,
                    'min_amount_for_delivery' => '',
                    'discount_percentage' => '',
                    'order_arrival_minutes' => 30,
                    'notification_sound' => 'buzzer-01.mp3',
                    'play_sound' => 0,
                    'auto_print' => 0,
                    'schedule_array' => '{"sunday":{"name":"Sun","enabled":false,"dayInWeek":0,"timesSchedules":[{"startTime":"","endTime":""}]},"monday":{"name":"Mon","enabled":false,"dayInWeek":1,"timesSchedules":[{"startTime":"","endTime":""}]},"tuesday":{"name":"Tue","enabled":false,"dayInWeek":2,"timesSchedules":[{"startTime":"","endTime":""}]},"wednesday":{"name":"Wed","enabled":false,"dayInWeek":3,"timesSchedules":[{"startTime":"","endTime":""}]},"thursday":{"name":"Thu","enabled":false,"dayInWeek":4,"timesSchedules":[{"startTime":"","endTime":""}]},"friday":{"name":"Fri","enabled":false,"dayInWeek":5,"timesSchedules":[{"startTime":"","endTime":""}]},"saturday":{"name":"Sat","enabled":false,"dayInWeek":6,"timesSchedules":[{"startTime":"","endTime":""}]}}',
                ];

                foreach($order_settings_keys as $key => $value)
                {
                    $settings = new OrderSetting();
                    $settings->shop_id = $shop->id;
                    $settings->key = $key;
                    $settings->value = $value;
                    $settings->save();
                }


                // Insert Payment Settings
                $payment_settings_keys = [
                    'paypal' => 0,
                    'paypal_mode' => 'sandbox',
                    'paypal_public_key' => '',
                    'paypal_private_key' => '',
                    'every_pay' => 0,
                    'everypay_mode' => 1,
                    'every_pay_public_key' => '',
                    'every_pay_private_key' => '',
                ];

                foreach($payment_settings_keys as $key => $value)
                {
                    $settings = new PaymentSettings();
                    $settings->shop_id = $shop->id;
                    $settings->key = $key;
                    $settings->value = $value;
                    $settings->save();
                }


                // Add Client Default Language
                $primary_lang = new LanguageSettings();
                $primary_lang->shop_id = $shop->id;
                $primary_lang->key = "primary_language";
                $primary_lang->value = $primary_language;
                $primary_lang->save();

                // Add Special Icon From Admin To Client
                $admin_special_icons = Ingredient::where('shop_id',NULL)->get();

                if(count($admin_special_icons) > 0)
                {
                    foreach($admin_special_icons as $sp_icon)
                    {
                        $sp_icon_id = (isset($sp_icon['id'])) ? $sp_icon['id'] : '';
                        $sp_icon_name = (isset($sp_icon['name'])) ? $sp_icon['name'] : '';
                        $sp_icon_status = (isset($sp_icon['status'])) ? $sp_icon['status'] : 0;
                        $sp_icon_image = (isset($sp_icon['icon'])) ? $sp_icon['icon'] : '';

                        $new_special_icon = new Ingredient();
                        $new_special_icon->shop_id = $shop->id;
                        $new_special_icon->parent_id = $sp_icon_id;
                        $new_special_icon->name = $sp_icon_name;
                        $new_special_icon->status = $sp_icon_status;
                        $new_special_icon->icon = $sp_icon_image;
                        $new_special_icon->save();

                        if(!empty($sp_icon_image) && file_exists('public/admin_uploads/ingredients/'.$sp_icon_image))
                        {
                            File::copy(public_path('admin_uploads/ingredients/'.$sp_icon_image), public_path('client_uploads/shops/'.$shop->shop_slug.'/ingredients/'.$sp_icon_image));
                        }
                    }
                }

                // Insert User Subscriptions
                if($subscription_id)
                {
                    $user_subscription = new UsersSubscriptions();
                    $user_subscription->user_id = $client->id;
                    $user_subscription->subscription_id = $subscription_id;
                    $user_subscription->duration = $duration;
                    $user_subscription->start_date = $current_date;
                    $user_subscription->end_date = $end_date;
                    $user_subscription->save();
                }

            }

            if($client->id && $shop->id)
            {
                $userShop = new UserShop();
                $userShop->user_id = $client->id;
                $userShop->shop_id = $shop->id;
                $userShop->save();
            }

            $verification_route = route('shop-verify',encrypt($client->id));

            // Sent Verification Mail to Customer
            $html .= '<div class="container" style="text-align:center">';
                $html .= '<h1>Your Shop Verification Token is : '.$user_verify_token.'</h1>';
                $html .= '<p>Verify Token to Verify your Shop Account.</p>';
                $html .= '<h2><a href="'.$verification_route.'">Verify Token Now</a></h2>';
            $html .= '</div>';

            $subject = "Shop Verification Mail";
            $from = 'info@smartqrscan.com';

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

            // More headers
            $headers .= 'From: <'.$from.'>' . "\r\n";

            mail($email,$subject,$html,$headers);

            return redirect()->route('shop-verify',encrypt($client->id))->with('success','Your Shop has been Registerd SuccessFully, Please Verify Your Shop.');
        }
        else
        {
            $validator = Validator::make([], []);
            $validator->getMessageBag()->add('captcha_response', 'CAPTCHA response is incorrect.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }


    public function ShopVerify($id)
    {
        try
        {
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
                return view('frontend.verify_shop',$data);
            }

        }
        catch (\Throwable $th)
        {
            return redirect()->route('home')->with('error','Something Went Wrong!');
        }
    }


    public function processShopVerify(Request $request)
    {
        $request->validate([
            'verification_token' => 'required',
        ]);

        $user_id = decrypt($request->user_id);
        $verify_token = $request->verification_token;

        $user = User::find($user_id);
        $user_verify_token = (isset($user['verify_token'])) ? $user['verify_token'] : '';

        if(!empty($user_verify_token) && ($user_verify_token == $verify_token))
        {
            $user->verify_token = NULL;
            $user->user_verify = 1;
            $user->update();

            return redirect()->route('home')->with('success','Your Shop has been Registerd SuccessFully and Shop has been Activated Soon.');
        }
        else
        {
            $validator = Validator::make([], []);
            $validator->getMessageBag()->add('verification_token', 'Please Enter a Valid Token to Verify Your Shop.');
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }
}
